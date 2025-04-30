<?php

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Modules\Communication\Http\Controllers\EmailController;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\DateFormat;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\NotificationType;
use Modules\GeneralSetting\Models\TimeFormat;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Modules\RolesPermission\Models\Module as ModuleModel;
use Modules\RolesPermission\Models\Permission;

if (!function_exists('clearCache')) {

    function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');

        return true;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile($file, $path = 'uploads', $oldFileName = '', $disk = 'public')
    {
        $disk = config('filesystems.default');

        if ($file instanceof UploadedFile && $file->isValid()) {
            if (Storage::disk($disk)->exists($path . '/' . $oldFileName)) {
                Storage::disk($disk)->delete($path . '/' . $oldFileName);
            }
            $filename = str_replace(',', '', Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension());
            $file->storeAs($path, $filename, $disk);
            return $path . "/" . $filename;
        }
        return null;
    }
}

if (!function_exists('uploadMutipleFile')) {
    function uploadMutipleFile($file, $path = 'uploads', $oldFileName = '', $disk = 'public')
    {
        $disk = config('filesystems.default');

        if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
            if ($oldFileName && Storage::disk($disk)->exists("$path/$oldFileName")) {
                Storage::disk($disk)->delete("$path/$oldFileName");
            }

            $filename = Str::uuid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($path, $filename, $disk);

            return $filename;
        }
        return null;
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime($date, $includeTime = true, $timeOnly = false)
    {
        // Fetch settings once
        $generalSettings = GeneralSetting::where('group_id', 5)->pluck('value', 'key');

        // Set default formats
        $dateFormat = 'Y-m-d';
        $timeFormat = 'H:i:s';

        // Get custom formats if available
        if ($generalSettings->isNotEmpty()) {
            $dateFormat = DateFormat::find($generalSettings->get('date_format'))?->name ?? $dateFormat;
            $timeFormat = TimeFormat::find($generalSettings->get('time_format'))?->name ?? $timeFormat;
        }

        // Determine the format based on flags
        $format = $timeOnly ? $timeFormat : ($includeTime ? "$dateFormat $timeFormat" : $dateFormat);

        // Apply format
        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $th) {
            return $date;
        }
    }
}

if (!function_exists('uploadedAsset')) {
    /**
     * Get the URL of an uploaded file or return full file details if requested.
     *
     * @param string|null $filePath The file path in storage.
     * @param string $default Type of default image ('profile' or other).
     * @param bool $fileFullDetails Whether to return full file details.
     * @return array|string File URL or full file details.
     */
    function uploadedAsset($filePath, $default = '', $fileFullDetails = false)
    {
        $disk = config('filesystems.default');

        // Use getBaseUrl() for consistent domain
        $baseUrl = getBaseUrl();

        // Default response structure
        $defaultImages = [
            'profile' => $baseUrl . '/assets/img/default-profile.png',
            'default2' => $baseUrl . '/assets/img/default-placeholder-image.png',
            'default' => $baseUrl . '/assets/img/default-image-02.jpg',
            'default_logo' => $baseUrl . '/assets/img/logo.svg',
            'default_small_logo' => $baseUrl . '/assets/img/logo-small.svg',
            'default_favicon' => $baseUrl . '/assets/img/favicon.png',

        ];

        // If file does not exist, return default image
        if (!$filePath || !Storage::disk($disk)->exists($filePath)) {
            return $fileFullDetails
                ? ['url' => $defaultImages[$default] ?? $defaultImages['default'], 'extension' => '', 'size' => 0]
                : ($defaultImages[$default] ?? $defaultImages['default']);
        }

        // Get file details
        $fileUrl = Storage::disk($disk)->url($filePath);
        $fileName = pathinfo($filePath, PATHINFO_BASENAME);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        $fileSize = Storage::disk($disk)->size($filePath);
        $formattedSize = formatFileSize($fileSize);

        // Format URL properly for public/local disks
        if ($disk === 'public' || $disk === 'local') {
            $fileUrl = $baseUrl . '/' . ltrim(parse_url($fileUrl, PHP_URL_PATH), '/');
        }

        return $fileFullDetails
            ? ['url' => $fileUrl, 'file_name' => $fileName, 'extension' => $fileExtension, 'size' => $formattedSize]
            : $fileUrl;
    }

}


function customEncrypt($data, $key = 'default_secret_key')
{
    $cipher = 'AES-128-CBC';
    $iv = substr(md5($key), 0, 16);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);
    return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
}

function customDecrypt($encryptedData, $key = 'default_secret_key')
{
    $cipher = 'AES-128-CBC';
    $iv = substr(md5($key), 0, 16);

    $encryptedData = strtr($encryptedData, '-_', '+/');
    $decrypted = openssl_decrypt(base64_decode($encryptedData), $cipher, $key, 0, $iv);

    return $decrypted;
}

function getDefaultCurrencySymbol()
{
    $defaultCurrency = GeneralSetting::where('key', 'currency_symbol')->first();
    $currencyId = $defaultCurrency->value ?? '';
    if ($currencyId) {
        $currency = Currency::find($currencyId);
        if ($currency) {
            return $currency->symbol;
        }
    }
    return '$';
}

function isRTL($languageCode = null)
{

    $language = TranslationLanguage::select('id')->where('code', $languageCode)->first();
    if ($language) {
        $languageId = $language->id;
        $language = Language::select('rtl')->where('language_id', $languageId)->first();
        if ($language) {
            return $language->rtl;
        }
    }
    return 0;
}

if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes)
    {
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }
}

if (!function_exists('getUserPermissions')) {
    function getUserPermissions($userId = null)
    {
        $user = $userId ? User::find($userId) : current_user();

        if (!$user || empty($user->role_id)) {
            return [];
        }

        return Permission::where('permissions.role_id', $user->role_id)
            ->whereHas('role', function ($query) {
                $query->where('status', 1);
            })
            ->select(
                'permissions.module_id',
                'permissions.create',
                'permissions.edit',
                'permissions.view',
                'permissions.delete',
                'permissions.allow_all'
            )
            ->with(['module:id,module_slug'])
            ->get();
    }
}

function hasPermission($permissions, $moduleSlug, $action)
{

    $user = current_user();

    $userType = $user->user_type ?? '';

    if ($userType == 1) {
        return true;
    }

    if (!$permissions) {
        return false;
    }

    $moduleSlugs = is_array($moduleSlug) ? $moduleSlug : [$moduleSlug];

    foreach ($moduleSlugs as $moduleSlug) {
        $permission = $permissions->firstWhere(function ($perm) use ($moduleSlug) {
            return $perm->module && $perm->module->module_slug == $moduleSlug;
        });
        if ($permission && isset($permission->$action) && $permission->$action == 1) {
            return true;
        }
    }

    return false;
}

function current_user($guard = null)
{
    $guard = $guard ?? Auth::getDefaultDriver();

    return Auth::guard($guard)->check()
        ? Auth::guard($guard)->user()
        : null;
}
function rentalNotificationEnabled()
{
    $bookingNotification = GeneralSetting::where('group_id', 2)->where('key', 'bookingUpdates')->first();
    if ($bookingNotification) {
        return $bookingNotification->value;
    }
    return 0;
}
function sendNotification($email, $slug, $notifyData = [])
{
    $notificationType = NotificationType::where('slug', $slug)->first();
    if (!$notificationType) {
        return null;
    }
    $placeholders = json_decode($notificationType->tags, true);
    $template = EmailTemplate::where('notification_type', $notificationType->id)
                ->where('status', 1)
                ->first();
    if (!$template) {
        return null;
    }

    $replaced = function ($text) use ($placeholders, $notifyData) {
        if (!$placeholders || !is_array($placeholders)) {
            return $text;
        }

        foreach ($placeholders as $tag) {
            $search = '{' . $tag . '}';
            $replace = $notifyData[$tag] ?? '';
            $text = str_replace($search, $replace, $text);
        }

        return $text;
    };

    if (!$email) {
        return null;
    }
    $parsedTemplate = [
        'subject'     => $replaced($template->subject),
        'content' => $replaced($template->description),
        'sms_content' => $replaced($template->sms_content),
        'notification_content' => $replaced($template->notification_content),
    ];

    if (!empty($parsedTemplate)) {
        $payload = [
            'to_email' => $email,
            'subject' => $parsedTemplate['subject'],
            'content' => $parsedTemplate['content'],
        ];
        $emailPayload   = new Request($payload);
        $emailController = new EmailController();
        $emailController->sendEmail($emailPayload);
        $user = User::where('email', $email)->first();
        if ($user) {
            Notification::create([
              'user_id' => $user->id,
              'subject' => $parsedTemplate['subject'],
              'content' => $parsedTemplate['notification_content']
            ]);
        }
    }
}

function getLanguageId($langCode = 'en')
{
    $languageId = TranslationLanguage::where('code', $langCode)->value('id');
    return $languageId ?? 1;
}

function getProfileImage()
{
    $user = current_user();
    if ($user) {
        $userDetails = $user->userDetail;
        if ($userDetails) {
            return uploadedAsset($userDetails->profile_image, 'profile');
        }
    }
}

function isAccessMenu($menu)
{
    $value = 0;
    if ($menu == 'reservation') {
        $value = GeneralSetting::where(['group_id' => 20, 'key' => 'reservation'])->pluck('value')->first();
    }
    if ($value) {
        return $value;
    }
    return 0;
}

if (!function_exists('getBaseUrl')) {
    function getBaseUrl()
    {
        if (app()->runningInConsole()) {
            return 1;
            return config('app.url');
        }

        return request()->getSchemeAndHttpHost();
    }
}
