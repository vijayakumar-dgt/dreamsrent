<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\GeneralSetting;
use Spatie\Sitemap\Tags\News;

class NewsletterController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.newsletters');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscriber_email' => [
                'required',
                Rule::unique('newsletter_subscribers', 'email')->whereNull('deleted_at')
            ],
        ], [
            'subscriber_email.required' => __('web.user.subscriber_email_required'),
            'subscriber_email.unique' => __('web.user.subscriber_email_unique'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            NewsletterSubscriber::create([
                'email' => $request->subscriber_email
            ]);

            try {
                $ownerName = GeneralSetting::where('key', 'owner_name')->value('value');
                $notifyData = [
                    'owner_name' => $ownerName ?? 'Admin',
                ];
                sendNewsletterEmail($request->subscriber_email, 'newsletter', $notifyData);
            } catch (\Exception $e) {
            }

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('web.user.newsletter_subscriber_create_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.common.default_create_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $userId = $this->authUser->id ?? $request->user_id;

            $columnIndex = $request->order[0]['column'] ?? 0;
            $columnName = $request->columns[$columnIndex]['data'] ?? 'email';
            $orderDir = $request->order[0]['dir'] ?? 'asc';

            $query = NewsletterSubscriber::select(
                'id',
                'email',
                'created_at',
            );

            if (!empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                });
            }

            if ($request->has('sort_by') && !empty($request->sort_by)) {
                switch (strtolower($request->sort_by)) {
                    case 'latest':
                        $query->orderBy('newsletter_subscribers.created_at', 'desc');
                        break;
                    case 'ascending':
                        $query->orderBy('newsletter_subscribers.id', 'asc');
                        break;
                    case 'descending':
                        $query->orderBy('newsletter_subscribers.id', 'desc');
                        break;
                    case 'last month':
                        $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                        $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                        $query->whereBetween('newsletter_subscribers.created_at', [$startDate, $endDate]);
                        break;
                    case 'last 7 days':
                        $startDate = \Carbon\Carbon::now()->subDays(7)->startOfDay();
                        $endDate = \Carbon\Carbon::now()->endOfDay();
                        $query->whereBetween('newsletter_subscribers.created_at', [$startDate, $endDate]);
                        break;
                }
            }

            $query->orderBy($columnName, $orderDir);

            $totalRecords = $query->count();
            $filteredRecords = $query->count();

            $query->offset($request->start)->limit($request->length);

            $reviews = $query->get()->map(function ($item) {
                $item->created_date = formatDateTime($item->created_at, false);
                return $item;
            });

            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => $totalRecords,
                "recordsFiltered" => $filteredRecords,
                "data" => $reviews,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('web.common.default_retrieve_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $id = $request->id;
            NewsletterSubscriber::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.others.newsletter_delete_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.common.default_delete_error')
            ], 500);
        }
    }

    public function sendNewsletter(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
            ],
        ], [
            'email.required' => __('admin.common.email_required'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        try {
            $email = $request->email;

            $user = Auth::guard('admin')->user();
            $userId = $user->id ?? null;

            $userDetail = UserDetail::where('user_id', $userId)->first();
            $name = ($userDetail && $userDetail->first_name) ? $userDetail->first_name . ' ' . $userDetail->last_name : 'Admin';

            $notifyData = [
                'user_name' => $name,
            ];

            sendNewsletterEmail($email, 'newsletter', $notifyData);

            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' => __('admin.others.newsletter_send_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 500,
                'message' => __('admin.others.newsletter_send_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
