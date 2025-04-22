<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\CommunicationSetting;
use Modules\GeneralSetting\Models\EmailTemplate;

class CommunicationSettingController extends Controller
{
    public function smsGateway(Request $request)
    {
        return view('generalsetting::system_settings.sms-gateway');
    }

    public function emailSettings(Request $request)
    {
        return view('generalsetting::system_settings.email_settings');
    }

    public function statusUpdate(Request $request): JsonResponse
    {
        $type = $request->gateway;
        $status = $request->status;

        $rules = [
            'gateway' => 'required|in:nexmo,twilio,twofactor,phpmail,smtp,sendgrid',
            'status' => 'required|in:0,1',
        ];

        $settype = "";
        if (in_array($type, ['nexmo', 'twilio', 'twofactor'])) {
            $settype = 2;
        } elseif (in_array($type, ['phpmail', 'smtp', 'sendgrid'])) {
            $settype = 1;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $saveSiteKey = CommunicationSetting::updateOrCreate(
                ['key' => $type . '_status'],
                ['value' => $status, 'settings_type' => $settype, 'type' => $type]
            );

            if ($status == 1) {
                CommunicationSetting::where('settings_type', $settype)
                    ->where('type', '!=', $type)
                    ->where('key', 'LIKE', '%_status')
                    ->update(['value' => 0]);
            }

            $message = $status == 1 ? __('admin.general_settings.activated_successfully') : __('admin.general_settings.deactivated_successfully');

            return response()->json(['code' => 200, 'message' => $message, 'data' => [$saveSiteKey]], 200);
        } catch (Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function smsList(Request $request): JsonResponse
    {
        $type = $request->input('type');

        try {
            $settings = CommunicationSetting::select('key', 'value', 'type')
                ->where('settings_type', $type)
                ->get();

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.data_retrived_successfully'),
                'data' => ['settings' => $settings]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' =>  __('admin.general_settings.data_failed_to_retrive'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeCommunicationSetting(Request $request): JsonResponse
    {
        try {
            $rules = $this->getValidationRules($request['type']);
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            
            $settingsType = $this->getSettingsType($request['type']);
            $settings = $this->getSettingsData($request);

            foreach ($settings as $key => $value) {
                $this->updateOrCreateSetting($key, $value, $settingsType, $request['type']);
            }

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.settings_update_success'),
                'data' => []
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => __('admin.common.default_update_error'),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getValidationRules(string $type): array
    {
        return match ($type) {
            'nexmo' => [
                'nexmo_api_key' => 'required|string',
                'nexmo_secret_key' => 'required|string',
                'nexmo_sender_id' => 'required|string',
            ],
            'twofactor' => [
                'twofactor_api_key' => 'required|string',
                'twofactor_secret_key' => 'required|string',
                'twofactor_sender_id' => 'required|string',
            ],
            'twilio' => [
                'twilio_api_key' => 'required|string',
                'twilio_secret_key' => 'required|string',
                'twilio_sender_id' => 'required|string',
            ],
            'smtp' => [
                'smtp_from_email' => 'required|string',
                'smtp_password' => 'required|string',
                'smtp_from_name' => 'required|string',
                'smtp_port' => 'required',
                'smtp_host' => 'required',
            ],
            'phpmail' => [
                'phpmail_from_email' => 'required|string',
                'phpmail_password' => 'required|string',
                'phpmail_from_name' => 'required|string',
            ],
            'sendgrid' => [
                'sendgrid_from_email' => 'required|string',
                'sendgrid_key' => 'required|string',
            ],
            'fcm' => [
                'project_id' => 'required|string',
                'client_email' => 'required|string',
                'private_key' => 'required|string',
            ],
            default => [],
        };
    }

    private function getSettingsType(string $type): int
    {
        return match ($type) {
            'nexmo', 'twofactor', 'twilio' => 2,
            'smtp', 'phpmail', 'sendgrid' => 1,
            'notification_settings', 'fcm' => 3,
            default => 0,
        };
    }

    private function getSettingsData(Request $request): array
    {
        return match ($request['type']) {
            'nexmo' => [
                'nexmo_api_key' => $request->nexmo_api_key,
                'nexmo_secret_key' => $request->nexmo_secret_key,
                'nexmo_sender_id' => $request->nexmo_sender_id,
            ],
            'twofactor' => [
                'twofactor_api_key' => $request->twofactor_api_key,
                'twofactor_secret_key' => $request->twofactor_secret_key,
                'twofactor_sender_id' => $request->twofactor_sender_id,
            ],
            'twilio' => [
                'twilio_api_key' => $request->twilio_api_key,
                'twilio_secret_key' => $request->twilio_secret_key,
                'twilio_sender_id' => $request->twilio_sender_id,
            ],
            'smtp' => [
                'smtp_from_email' => $request->smtp_from_email,
                'smtp_password' => $request->smtp_password,
                'smtp_from_name' => $request->smtp_from_name,
                'smtp_port' => $request->smtp_port,
                'smtp_host' => $request->smtp_host,
            ],
            'phpmail' => [
                'phpmail_from_email' => $request->phpmail_from_email,
                'phpmail_password' => $request->phpmail_password,
                'phpmail_from_name' => $request->phpmail_from_name,
            ],
            'sendgrid' => [
                'sendgrid_from_email' => $request->sendgrid_from_email,
                'sendgrid_key' => $request->sendgrid_key,
            ],
            'fcm' => [
                'project_id' => $request->project_id,
                'client_email' => $request->client_email,
                'private_key' => $request->private_key,
            ],
            'notification_settings' => [
                'emailNotifications' => isset($request->emailNotifications) && $request->emailNotifications == 'on' ? 1 : 0,
                'pushNotifications' => isset($request->pushNotifications) && $request->pushNotifications == 'on' ? 1 : 0,
                'smsNotifications' => isset($request->smsNotifications) && $request->smsNotifications == 'on' ? 1 : 0,
            ],
            default => [],
        };
    }

    private function updateOrCreateSetting(string $key, string|int $value, int $settingsType, string $type)
    {
        return CommunicationSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'settings_type' => $settingsType, 'type' => $type]
        );
    }

    public function sendTestMail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_address' => [
                'required',
                'email',
            ],
        ], [
            'email_address.required' => __('admin.general_settings.email_address_required'),
            'email_address.email' => __('admin.common.email_valid'),
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'code'   => 422,
                'errors' => $validator->errors()->toArray()
            ],422);
        }

        try {
            $user = Auth::guard('admin')->user();
            $userId = $user->id ?? null;
            $name = $user->name ?? '';

            $userDetail = UserDetail::where('user_id', $userId)->first();
            if ($userDetail) {
                $name = $userDetail->first_name . ' ' . $userDetail->last_name;
            }

            $name = $name ?? 'Admin';

            $template = EmailTemplate::select('subject', 'description')
                ->where('notification_type', 7)
                ->first();

            $data = [
                'subject' => $template->subject ?? 'Reg - Admin Test Mail',
                'content' => $template->description ?? "Hello $name,<br><br>
                This is a test email to confirm that the email configuration for admin notifications is working correctly.<br><br>
                If you have received this email, everything is set up properly on your end. No further action is required.<br><br>
                Regards,<br>
                System Administrator",
                'to_email' => $request->email_address
            ];

            $requestData = New Request($data);

            $emailController = new EmailController();
            $emailController->sendEmail($requestData);

            return response()->json([
                'code' => 200,
                'message' => __('admin.general_settings.test_mail_sent_success'),
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 400,
                'message' => __('admin.general_settings.test_mail_sent_fail'),
                'error' => $th->getMessage()
            ], 400);
        }
    }


}
