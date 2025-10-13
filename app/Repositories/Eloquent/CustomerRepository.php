<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserDocument;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\Booking\Models\Booking;
use Modules\GeneralSetting\Models\Language;

class CustomerRepository implements CustomerRepositoryInterface
{
    public const DOCUMENTS_SELECT = 'documents:id,user_id,document';
    public const TRANSLATION_LANG_CODE = 'translation_languages.code as language_code';
    public const TRANSLATION_LANG_NAME = 'translation_languages.name as language_name';

    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }

    public function index(): array
    {
        $languages = Language::select('languages.language_id')
            ->with([
                'transLang' => function ($query) {
                    $query->select('id', 'code', 'name');
                }
            ])
            ->where('languages.status', 1)
            ->get();

        return ['languages' => $languages];
    }

    public function store(Request $request): array
    {
        $id = $request->id ?? '';
        $isNew = empty($id);

        $successMsg = $isNew
            ? __('admin.manage.customer_create_success')
            : __('admin.manage.customer_update_success');
        $errorMsg = $isNew
            ? __('admin.common.default_create_error')
            : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            // Create user data arrays
            $userData = [
                'email'       => $request->email,
                'phone_number'=> $request->phone_number,
                'user_type'   => 3,
                'language_id' => $request->language,
            ];
            $userDetailsData = [
                'first_name'    => $request->first_name,
                'last_name'     => $request->last_name,
                'gender'        => $request->gender,
                'dob'           => Carbon::createFromFormat('d-m-Y', $request->dob),
                'address'       => $request->address,
                'card_number'   => $request->card_number,
                'date_of_issue' => Carbon::createFromFormat('d-m-Y', $request->date_of_issue),
                'valid_date'    => Carbon::createFromFormat('d-m-Y', $request->valid_date),
            ];

            // Handle profile image
            $this->handleProfileImage($request, $userDetailsData, $isNew, $id);

            // Create or update user
            $userId = $isNew ? $this->createUser($userData, $userDetailsData) : $this->updateUser($id, $userData, $userDetailsData);

            // Handle documents
            $this->handleDocuments($request->file('documents'), $userId);
            $this->removeDocuments($request->removed_documents);

            DB::commit();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => $successMsg,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();

            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => $errorMsg,
            ];
        }
    }

    // ====================== Helper methods ======================
    private function handleProfileImage(Request $request, array &$userDetailsData, bool $isNew, $id): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $file = $request->file('image');
        if (!$file instanceof UploadedFile) {
            return;
        }

        $oldImage = !$isNew ? UserDetail::where('user_id', $id)->value('profile_image') : '';
        $userDetailsData['profile_image'] = $this->imageResizer->uploadFile($file, 'profile', $oldImage);
    }

    private function createUser(array $userData, array $userDetailsData): int
    {
        $user = User::create($userData);
        $userDetailsData['user_id'] = $user->id;
        UserDetail::create($userDetailsData);
        return $user->id;
    }

    private function updateUser(int $id, array $userData, array $userDetailsData): int
    {
        User::where('id', $id)->update($userData);
        UserDetail::updateOrCreate(['user_id' => $id], $userDetailsData);
        return $id;
    }

    private function handleDocuments($files, int $userId): void
    {
        foreach ((array)$files as $file) {
            $document = uploadFile($file, 'documents');
            UserDocument::create([
                'user_id'  => $userId,
                'document' => $document,
            ]);
        }
    }

    private function removeDocuments(?string $removedDocuments): void
    {
        $removedDocuments = array_filter(explode(',', $removedDocuments ?? ''));
        foreach ($removedDocuments as $docId) {
            $doc = UserDocument::find($docId);
            if (!$doc) {
                continue;
            }

            if (!empty($doc->document) && Storage::disk('public')->exists($doc->document)) {
                Storage::disk('public')->delete($doc->document);
            }
            $doc->delete();
        }
    }

    public function list(Request $request): array
    {
        try {
            $start       = $request->start ?? 0;
            $length      = $request->length ?? 10;
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName  = $request->columns[$columnIndex]['data'] ?? 'customer_full_name';
            $orderDir    = strtoupper($request->order[0]['dir'] ?? 'ASC') === 'DESC' ? 'DESC' : 'ASC';

            $query = $this->baseUserQuery();

            $this->applySearchFilter($query, $request->search ?? null);
            $this->applyLanguageFilter($query, $request->language ?? null);
            $this->applyStatusFilter($query, $request->sort_by_status ?? null);
            $this->applyDateFilter($query, $request->sort_by_date ?? null);
            $this->applyCustomSort($query, $request->sort_by ?? null);

            $this->applyColumnSorting($query, $columnName, $orderDir);

            $totalRecords    = User::where('user_type', 3)->count();
            $filteredRecords = $query->count();

            $users = $query->offset($start)->limit($length)->get()->map(function ($user) {
                return $this->mapUserData($user);
            });

            return [
                'draw'            => intval($request->draw),
                'recordsTotal'    => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data'            => $users,
                'code'            => 200,
            ];
        } catch (\Exception $e) {
            return [
                'code'    => 500,
                'message' => __('admin.common.default_retrieve_error'),
            ];
        }
    }

    private function baseUserQuery()
    {
        return User::with([self::DOCUMENTS_SELECT])
            ->select(
                'users.id',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
                'users.email',
                'users.phone_number',
                'users.status',
                'user_details.profile_image',
                'user_details.dob',
                'user_details.gender',
                'user_details.address',
                'users.language_id',
                self::TRANSLATION_LANG_NAME,
                self::TRANSLATION_LANG_CODE,
            )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('translation_languages', 'translation_languages.id', '=', 'users.language_id')
            ->where('users.user_type', 3)
            ->whereNull('users.deleted_at');
    }

    private function applySearchFilter($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('users.email', 'LIKE', "%{$search}%")
                ->orWhere('users.phone_number', 'LIKE', "%{$search}%")
                ->orWhere('user_details.first_name', 'LIKE', "%{$search}%")
                ->orWhere('user_details.last_name', 'LIKE', "%{$search}%");
            });
        }
    }

    private function applyLanguageFilter($query, $languages)
    {
        if (!empty($languages)) {
            $query->whereIn('users.language_id', (array)$languages);
        }
    }

    private function applyStatusFilter($query, $status)
    {
        if (isset($status)) {
            $query->where('users.status', $status);
        }
    }

    private function applyDateFilter($query, $dateRange)
    {
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) === 2) {
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate   = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                $query->whereBetween('users.created_at', [$startDate, $endDate]);
            }
        }
    }

    private function applyCustomSort($query, $sortBy)
    {
        if (!$sortBy) {
            return;
        }

        switch (strtolower($sortBy)) {
            case 'ascending':
                $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) ASC");
                break;
            case 'descending':
                $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) DESC");
                break;
            case 'last month':
                $query->whereBetween('users.created_at', [
                    \Carbon\Carbon::now()->subMonth()->startOfMonth(),
                    \Carbon\Carbon::now()->subMonth()->endOfMonth()
                ]);
                break;
            case 'last 7 days':
                $query->whereBetween('users.created_at', [
                    \Carbon\Carbon::now()->subDays(7)->startOfDay(),
                    \Carbon\Carbon::now()->endOfDay()
                ]);
                break;
            default:
                $query->orderBy('users.created_at', 'DESC');
        }
    }

    private function applyColumnSorting($query, $columnName, $orderDir)
    {
        if ($columnName === 'customer_full_name') {
            $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name)) {$orderDir}");
        } else {
            $allowedColumns = ['email', 'phone_number', 'status', 'language_id'];
            $col = in_array($columnName, $allowedColumns) ? "users.$columnName" : 'users.created_at';
            $query->orderBy($col, $orderDir);
        }
    }

    private function mapUserData($user)
    {
        $user->valid_date    = formatDateTime($user->valid_date, false);
        $user->date_of_issue = formatDateTime($user->date_of_issue, false);
        $user->profile_image = uploadedAsset($user->profile_image ?? null, 'profile');
        $user->language_flag = url("/backend/assets/img/flags/{$user->language_code}.svg");
        $user->encrypted_id  = customEncrypt($user->id, User::$userSecretKey);

        $user->documents->map(function ($document) {
            $document->document_url = uploadedAsset($document->document, 'documents');
            return $document;
        });

        $user->customer_full_name = trim($user->customer_full_name) === '' ? '' : ucwords($user->customer_full_name);

        return $user;
    }

    public function edit(int $id): array
    {
        $data = User::with([self::DOCUMENTS_SELECT])
            ->select(
                'users.id',
                'users.email',
                'users.phone_number',
                'users.status',
                'user_details.first_name',
                'user_details.last_name',
                'user_details.profile_image',
                'user_details.dob',
                'user_details.gender',
                'user_details.address',
                'users.language_id',
                self::TRANSLATION_LANG_NAME,
                self::TRANSLATION_LANG_CODE,
                'user_details.valid_date',
                'user_details.date_of_issue',
                'user_details.card_number',
            )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('translation_languages', 'translation_languages.id', '=', 'users.language_id')
            ->where(['users.user_type' => 3, 'users.id' => $id])
            ->first();

        if ($data) {
            $data->profile_image = is_string($data->profile_image) || is_null($data->profile_image)
                ? uploadedAsset($data->profile_image, 'profile')
                : uploadedAsset(null, 'profile');
            $data->valid_date = Carbon::parse($data->valid_date)->format('d-m-Y');
            $data->date_of_issue = Carbon::parse($data->date_of_issue)->format('d-m-Y');
            $data->dob = Carbon::parse($data->dob)->format('d-m-Y');
            /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDocument> $documents */
            $documents = $data->documents;
            $documents->map(function (UserDocument $document) {
                $document->document_url = uploadedAsset($document->document, 'documents');
                return $document;
            });
        }

        return [
            'status' => 'success',
            'code'   => 200,
            'data'   => $data
        ];
    }

    public function getCustomerDetails(?int $id): array
    {
        $customer = User::with([self::DOCUMENTS_SELECT])
            ->select(
                'users.id',
                'users.email',
                'users.phone_number',
                'users.status',
                'users.created_at',
                DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
                'user_details.profile_image',
                'user_details.dob',
                'user_details.gender',
                'user_details.address',
                'users.language_id',
                self::TRANSLATION_LANG_NAME,
                self::TRANSLATION_LANG_CODE,
                'user_details.valid_date',
                'user_details.date_of_issue',
                'user_details.card_number',
            )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('translation_languages', 'translation_languages.id', '=', 'users.language_id')
            ->where(['users.user_type' => 3, 'users.id' => $id])
            ->firstOrFail();

        $bookings = Booking::select(
            'bookings.id',
            'bookings.reservation_id',
            'vehicle_info.name as vehicle_name',
            'vehicle_info.vehicle_image',
            'bookings.booking_date',
            'bookings.final_price',
        )
            ->join('vehicle_info', 'vehicle_info.id', '=', 'bookings.vehicle_id')
            ->where('bookings.customer_id', $id)
            ->orderBy('bookings.id', 'desc')
            ->limit(10)
            ->get()->map(function ($booking) {
                $booking->booking_date = formatDateTime($booking->booking_date);
                $booking->vehicle_image_url = uploadedAsset($booking->vehicle_image, 'default');
                return $booking;
            });

        $bookingHistories = Booking::select(
            'bookings.id',
            'booking_histories.created_at',
            'booking_histories.action',
            'booking_histories.message',
        )
            ->join('booking_histories', 'booking_histories.booking_id', '=', 'bookings.id')
            ->where('bookings.customer_id', $id)
            ->get();

        $defaultCurrency = getDefaultCurrencySymbol();
        if ($customer) {
            $customer->profile_image = is_string($customer->profile_image) || is_null($customer->profile_image)
                ? uploadedAsset($customer->profile_image, 'profile')
                : uploadedAsset(null, 'profile');
            $customer->valid_date = $customer->valid_date ? formatDateTime($customer->valid_date, false) : null;
            $customer->dob = $customer->dob ? formatDateTime($customer->dob, false) : null;
            $customer->added_on = formatDateTime($customer->created_at);

            /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDocument> $documents */
            $documents = $customer->documents;
            $documents->map(function (UserDocument $document) {
                $fileDetails = uploadedAssetDetails($document->document, '');
                $document->file_name = $fileDetails['file_name'] ?? null;
                $document->size = $fileDetails['size'] ?? '';
                $document->extension = $fileDetails['extension'] ?? '';
                $document->document_url = uploadedAsset($document->document, '');
                $document->icon = url('backend/assets/img/file-icon.svg');
                return $document;
            });
        }

        return [
            'customer'         => $customer,
            'bookings'         => $bookings,
            'bookingHistories' => $bookingHistories,
            'defaultCurrency'  => $defaultCurrency,
        ];
    }

    public function delete(Request $request): array
    {
        try {
            $id = $request->id;
            $ids = $request->ids ?? [];

            if ($request->has('ids') && !empty($ids)) {
                User::whereIn('id', $ids)->delete();
                UserDetail::whereIn('user_id', $ids)->delete();
            } else {
                User::where('id', $id)->delete();
                UserDetail::where('user_id', $id)->delete();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.user_management.user_delete_success')
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('admin.common.default_delete_error')
            ];
        }
    }
}
