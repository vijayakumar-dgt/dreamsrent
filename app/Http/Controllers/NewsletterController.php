<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\EmailTemplate;
use Spatie\Sitemap\Tags\News;

class NewsletterController extends Controller
{
    public function index(Request $request)
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

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ],422);
        }

        try {

            NewsletterSubscriber::create([
                'email' => $request->subscriber_email
            ]);

            $data = [
                'to_email' => $request->subscriber_email,
                'subject' => $template->subject ?? 'Reg - Newsletter',
                'content' => 'You have been subscribed to our newsletter.',
            ];

            $request = new Request($data);
            $emailController = new EmailController();
            $emailController->sendEmail($request);

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
            ],500);
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
            ],500);
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
            ],500);
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

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ],422);
        }

        try {
            $email = $request->email;

            $notificationType = 3;
            $template = EmailTemplate::select('subject', 'description')
                    ->where('notification_type', $notificationType)
                    ->first();

            $data = [
                'subject' => $template->subject ?? 'Reg - Newsletter',
                'content' => $template->description ?? 'You have successfully subscribed to our newsletter.',
                'to_email' => $email
            ];

            $requestData = New Request($data);

            $emailController = new EmailController();
            $emailController->sendEmail($requestData);

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
            ],500);
        }
    }

}
