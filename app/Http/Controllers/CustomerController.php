<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserDocument;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingHistory;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;

class CustomerController extends Controller
{
    public function index(Request $request): View | JsonResponse
    {
        $languages = Language::select('languages.language_id')
            ->with(['transLang' => function ($query) {
                $query->select('id', 'code', 'name');
            }])
            ->where('languages.status', 1)
            ->get();

        return view('admin.customers', compact('languages'));
    }

    public function store(Request $request): JsonResponse
    {
        $id = $request->id ?? '';

        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'max:100',
                Rule::unique('users', 'name')->ignore($id)->whereNull('deleted_at'),
            ],
            'first_name' => [
                'required',
                'min:3',
                'max:20',
            ],
            'last_name' => [
                'required',
                'max:20',
            ],
            'gender' => ['required'],
            'phone_number' => ['required'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($id)->whereNull('deleted_at'),
            ],
            'address' => ['required', 'max:150'],
            'card_number' => [
                'required',
                Rule::unique('user_details', 'card_number')->ignore($id, 'user_id')->whereNull('deleted_at'),
            ],
            'image' => 'mimes:jpeg,jpg,png|max:2048',
            'date_of_issue' => ['required'],
            'valid_date' => ['required'],
            'documents.*' => 'file|mimes:jpeg,jpg,png,pdf,doc,docx|max:5120',
        ], [
            'first_name.required' => __('admin.common.first_name_required'),
            'first_name.min' => __('admin.common.first_name_minlength', ['min' => 3]),
            'first_name.max' => __('admin.common.first_name_maxlength', ['max' => 30]),
            'last_name.required' => __('admin.common.last_name_required'),
            'last_name.min' => __('admin.common.last_name_minlength', ['min' => 3]),
            'last_name.max' => __('admin.common.last_name_maxlength', ['max' => 30]),
            'username.required' => __('admin.common.username_required'),
            'username.max' => __('admin.common.username_maxlength'),
            'username.unique' => __('admin.common.username_unique'),
            'gender.required' => __('admin.manage.gender_required'),
            'phone_number.required' => __('admin.common.phone_number_required'),
            'address.required' => __('admin.manage.address_required'),
            'address.max' => __('admin.manage.address_maxlength'),
            'image.mimes' => __('admin.common.image_format'),
            'image.max' => __('admin.common.image_size', ['size' => 2]),
            'documents.*.mimes' => __('admin.manage.documents_format'),
            'documents.*.max' => __('admin.manage.documents_size', ['size' => 5]),
            'card_number.required' => __('admin.manage.card_number_required'),
            'card_number.unique' => __('admin.manage.card_number_unique'),
            'date_of_issue.required' => __('admin.manage.date_of_issue_required'),
            'valid_date.required' => __('admin.manage.valid_date_required'),
            'email.required' => __('admin.common.email_required'),
            'email.email' => __('admin.common.email_valid'),
            'email.unique' => __('admin.common.email_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $successMsg = empty($id) ? __('admin.manage.customer_create_success') : __('admin.manage.customer_update_success');
        $errorMsg = empty($id) ?  __('admin.common.default_create_error') : __('admin.common.default_update_error');

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'user_type' => 3,
                'language_id' => $request->language,
            ];
            $userDetailsData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'dob' => Carbon::createFromFormat('d-m-Y', $request->dob),
                'address' => $request->address,
                'card_number' => $request->card_number,
                'date_of_issue' => Carbon::createFromFormat('d-m-Y', $request->date_of_issue),
                'valid_date' => Carbon::createFromFormat('d-m-Y', $request->valid_date),
            ];

            if (empty($id)) {
                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if ($file instanceof UploadedFile) {
                        $userDetailsData['profile_image'] = uploadFile($file, 'profile');
                    }
                }
                $user = User::create($userData);

                $userDetailsData['user_id'] = $user->id;
                UserDetail::create($userDetailsData);

                /** @var UploadedFile[]|UploadedFile|null $files */
                $files = $request->file('documents');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $document = uploadFile($file, 'documents');
                        UserDocument::create([
                            'user_id' => $user->id,
                            'document' => $document,
                        ]);
                    }
                }
            } else {
                $user = UserDetail::where('user_id', $id)->first();
                $oldImage = '';
                if ($user) {
                    $oldImage = $user->profile_image;
                }

                if ($request->hasFile('image')) {
                    $file = $request->file('image');
                    if ($file instanceof UploadedFile) {
                        $userDetailsData['profile_image'] = uploadFile($file, 'profile', $oldImage);
                    }
                }

                /** @var UploadedFile[]|UploadedFile|null $files */
                $files = $request->file('documents');
                if (is_array($files)) {
                    foreach ($files as $file) {
                        $document = uploadFile($file, 'documents');
                        UserDocument::create([
                            'user_id' => $id,
                            'document' => $document,
                        ]);
                    }
                }
                $removedDocuments = array_filter(explode(',', $request->removed_documents));
                if (!empty($removedDocuments)) {
                    foreach ($removedDocuments as $docId) {
                        $removedDocument = UserDocument::find($docId);
                        if ($removedDocument) {
                            $doc = $removedDocument->document;

                            if (!empty($doc) && Storage::disk('public')->exists($doc)) {
                                Storage::disk('public')->delete($doc);
                            }

                            $removedDocument->delete();
                        }
                    }
                }
                user::where('id', $id)->update($userData);
                UserDetail::updateOrCreate(
                    ['user_id' => $id],
                    $userDetailsData
                );
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => $successMsg
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => $errorMsg,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $searchValue = $request->search ?? null;
            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'customer_full_name';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = User::with(['documents:id,user_id,document'])
                ->select(
                    'users.id',
                    'users.name as username',
                    DB::raw("CONCAT(user_details.first_name, ' ', user_details.last_name) as customer_full_name"),
                    'users.email',
                    'users.phone_number',
                    'users.status',
                    'user_details.profile_image',
                    'user_details.dob',
                    'user_details.gender',
                    'user_details.address',
                    'users.language_id',
                    'translation_languages.name as language_name',
                    'translation_languages.code as language_code',
                )
                ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
                ->leftJoin('translation_languages', 'translation_languages.id', '=', 'users.language_id')
                ->where(['users.user_type' => 3]);

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.email', 'LIKE', "%{$search}%")
                        ->orWhere('users.phone_number', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.first_name', 'LIKE', "%{$search}%")
                        ->orWhere('user_details.last_name', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('language') && !empty($request->language)) {
                $query->whereIn('users.language_id', $request->language);
            }

            if ($request->has('sort_by_status') && !empty($request->sort_by_status) || $request->sort_by_status == '0') {
                $status = $request->sort_by_status;
                $query->where('users.status', $status);
            }

            if ($request->has('sort_by_date') && !empty($request->sort_by_date)) {
                $dates = explode(' - ', $request->sort_by_date);
                if (count($dates) === 2) {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]));
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]));

                    if ($startDate && $endDate) {
                        $query->whereBetween('users.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
                    }
                }
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('users.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('users.id', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('users.id', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('users.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('users.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            if ($columnName === 'customer_full_name') {
                $query->orderByRaw("LOWER(CONCAT_WS(' ', user_details.first_name, user_details.last_name, users.name)) {$orderDir}");
            } else {
                $query->orderBy($columnName, $orderDir);
            }

            $totalRecords = User::where(['users.user_type' => 3])->count();
            $filteredRecords = $query->count();

            $query->offset($start)->limit($length);
            $users = $query->get();

            $users->map(function ($user) {
                $user->valid_date = formatDateTime($user->valid_date, false);
                $user->date_of_issue = formatDateTime($user->date_of_issue, false);
                $user->profile_image = is_string($user->profile_image) || is_null($user->profile_image)
                    ? uploadedAsset($user->profile_image, 'profile')
                    : uploadedAsset(null, 'profile');
                $user->language_flag = url('/assets/img/flags/' . $user->language_code . '.svg');
                $user->encrypted_id = customEncrypt($user->id, User::$userSecretKey);

                /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserDocument> $documents */
                $documents = $user->documents;
                $documents->map(function (UserDocument $document) {
                    $document->document_url = uploadedAsset($document->document, 'documents');
                    return $document;
                });

                if ($user->customer_full_name == ' ') {
                    $user->customer_full_name = '';
                }

                return $user;
            });

            return response()->json([
                'draw' => intval($request->draw),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_retrieve_error'),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $id = $request->id;

        $data = User::with(['documents:id,user_id,document'])
            ->select(
                'users.id',
                'users.name as username',
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
                'translation_languages.name as language_name',
                'translation_languages.code as language_code',
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

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $data
        ], 200);
    }

    public function customerDetails(Request $request): View
    {
        $id = customDecrypt($request->id, User::$userSecretKey);
        $customer = User::with(['documents:id,user_id,document'])
            ->select(
                'users.id',
                'users.name as username',
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
                'translation_languages.name as language_name',
                'translation_languages.code as language_code',
                'user_details.valid_date',
                'user_details.date_of_issue',
                'user_details.card_number',
            )
            ->leftJoin('user_details', 'users.id', '=', 'user_details.user_id')
            ->leftJoin('translation_languages', 'translation_languages.id', '=', 'users.language_id')
            ->where(['users.user_type' => 3, 'users.id' => $id])
            ->first();

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
                $fileDetails = uploadedAsset($document->document, '', true);
                $document->file_name = $fileDetails['file_name'] ?? null;
                $document->size = $fileDetails['size'] ?? '';
                $document->extension = $fileDetails['extension'] ?? '';
                $document->document_url = uploadedAsset($document->document, '');
                $document->icon = url('assets/img/file-icon.svg');
                return $document;
            });
        }

        return view('admin.customer_details', compact('customer', 'bookings', 'id', 'defaultCurrency', 'bookingHistories'));
    }

    public function delete(Request $request): JsonResponse
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

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.user_management.user_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }
}
