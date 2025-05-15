<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Email;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\NotificationTag;
use Modules\GeneralSetting\Models\NotificationType;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tags = NotificationTag::where('status', true)->get();
        $notificationTypes = NotificationType::where('status', true)->get();
        return view('generalsetting::system_settings.email_template', compact('tags', 'notificationTypes'));
    }

    public function store(Request $request): JsonResponse
    {
        $existing = EmailTemplate::where('notification_type', $request->notification_type)
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->whereNull('deleted_at')
            ->first();

        $rules = [
            'title' => 'required',
            'notification_type' => 'required',
            'subject' => 'required|string|max:255',
            'sms_content' => 'required|string|max:500',
            'description' => 'required',
        ];

        $messages = [
            'description.required' =>  __('admin.general_settings.description_not_empty'),
            'subject.required' =>  __('admin.general_settings.subject_required'),
            'sms_content.required' => __('admin.general_settings.sms_required'),
        ];

        if ($existing) {
            $rules['notification_type'] = ['required', function ($attribute, $value, $fail) {
                $fail('An email template already exists for the selected notification type.');
            }];
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'errors' => $validator->errors()->toArray()
            ], 422);
        }

        $description = strip_tags(trim($request->description));
        if (empty($description)) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => __('admin.general_settings.description_not_empty'),
                'errors' => [
                    'description' => ['Description cannot be empty']
                ]
            ], 422);
        }

        try {
            $successMessage =  __('admin.general_settings.email_templated_success');

            if ($request->has('id') && $request->id != '') {
                /** @var \Modules\GeneralSetting\Models\EmailTemplate $emailTemplate */
                $emailTemplate = EmailTemplate::find($request->id);
                /** @var EmailTemplate|null $emailTemplate */
                if (!$emailTemplate) {
                    return response()->json([
                        'status' => 'error',
                        'code' => 500,
                        'message' => __('admin.general_settings.email_template_not_found'),
                    ]);
                }
                $emailTemplate->status = $request->has('status') && $request->status == 'on' ? 1 : 0;
                $successMessage = __('admin.general_settings.email_template_updated_success');
            } else {
                $emailTemplate = new EmailTemplate();
            }

            $emailTemplate->title = $request->title;
            $emailTemplate->notification_type = $request->notification_type;
            $emailTemplate->subject = $request->subject;
            $emailTemplate->sms_content = $request->sms_content;
            $emailTemplate->notification_content = $request->notification_content;
            $emailTemplate->description = $request->description;
            $emailTemplate->save();

            return response()->json([
                'status' => 'success',
                'code' => 200,
                'message' => $successMessage
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code' => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function getEmailTemplates(Request $request): JsonResponse
    {
        $pageLength = $request->length;
        $offset     = $request->start;
        $emailTemplates = EmailTemplate::query();
        if ($request->has('keyword') && $request->keyword != "") {
            $emailTemplates = $emailTemplates->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            });
        }
        $emailTemplates = $emailTemplates->orderBy('id', 'desc');
        $emailTemplates = $emailTemplates->skip($offset)->take($pageLength)->get()->map(function ($emailTemplate) {
            return [
                'id' => $emailTemplate->id,
                'title' => $emailTemplate->title,
                'notification_type' => $emailTemplate->notification_type,
                'subject' => $emailTemplate->subject,
                'sms_content' => $emailTemplate->sms_content,
                'notification_content' => $emailTemplate->notification_content,
                'description' => $emailTemplate->description,
                'status' => $emailTemplate->status,
                'formated_date' => formatDateTime($emailTemplate->created_at)
            ];
        });
        $totalRecords = $filteredRecords = EmailTemplate::count();
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $emailTemplates
        ]);
    }


    public function getEmailTemplate(?int $id): JsonResponse
    {
        $emailTemplate = EmailTemplate::find($id);
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $emailTemplate
        ], 200);
    }

    public function deleteEmailTeplate(Request $request): JsonResponse
    {
        try {
            /** @var \Modules\GeneralSetting\Models\EmailTemplate $template */
            $template = EmailTemplate::findOrFail($request->id);
            $template->delete();
            return response()->json([
                'status' => 'success',
                'code'   => 200,
                'message' =>  __('admin.general_settings.email_template_deleted_success'),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => 'Currency not found'
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'message' => $th->getMessage()
            ], 422);
        }
    }

    public function getTags(?int $id): JsonResponse
    {
        $notificationType = NotificationType::find($id);
        $defaultTags = NotificationTag::where('status', true)->pluck('title')->toArray();
        $tags = $notificationType && $notificationType->tags ? json_decode($notificationType->tags) : $defaultTags;
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $notificationType,
            'tags'   => $tags
        ]);
    }
}
