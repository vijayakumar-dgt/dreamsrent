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
use Illuminate\Support\Collection;
use Illuminate\Contracts\Auth\Authenticatable;

if (!function_exists('clearCache')) {

    function clearCache(): bool
    {
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('optimize:clear');

        return true;
    }
}

if (!function_exists('uploadFile')) {
    function uploadFile(UploadedFile $file, string $path = 'uploads', ?string $oldFileName = '', string $disk = 'public'): ?string
    {
        $disk = config('filesystems.default');

        if ($file->isValid()) {
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
    function uploadMutipleFile(UploadedFile $file, string $path = 'uploads', ?string $oldFileName = '', string $disk = 'public'): ?string
    {
        $disk = config('filesystems.default');

        if ($file->isValid()) {
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
    function formatDateTime(mixed $date, bool $includeTime = true, bool $timeOnly = false): string
    {
        $generalSettings = GeneralSetting::where('group_id', 5)->pluck('value', 'key');

        $dateFormat = 'Y-m-d';
        $timeFormat = 'H:i:s';

        if ($generalSettings->isNotEmpty()) {
            $dateFormat = optional(DateFormat::find($generalSettings->get('date_format')))->name ?? $dateFormat;
            $timeFormat = optional(TimeFormat::find($generalSettings->get('time_format')))->name ?? $timeFormat;
        }

        $format = $timeOnly ? $timeFormat : ($includeTime ? "$dateFormat $timeFormat" : $dateFormat);

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $th) {
            return (string) $date;
        }
    }
}

if (!function_exists('uploadedAsset')) {
    
    /**
     * @param string $filePath
     * @param string $default
     * @param bool $fileFullDetails
     * @return string|array{url: string, file_name?: string, extension?: string, size?: string}
     */
    function uploadedAsset(?string $filePath, ?string $default = '', bool $fileFullDetails = false): string|array
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
            'default_small_logo' => $baseUrl . '/frontend/assets/img/logo-small.png',
            'default_favicon' => $baseUrl . '/assets/img/favicon.png',
        ];

        // If file does not exist, return default image
        if (!$filePath || !Storage::disk($disk)->exists($filePath)) {
            return $fileFullDetails
                ? ['url' => $defaultImages[$default] ?? $defaultImages['default'], 'extension' => '', 'size' => '0']
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
            // Ensure that parse_url returns a valid string before calling ltrim
            $urlPath = parse_url($fileUrl, PHP_URL_PATH);
            $fileUrl = $baseUrl . '/' . (is_string($urlPath) ? ltrim($urlPath, '/') : '');
        }
        return $fileFullDetails
            ? ['url' => $fileUrl, 'file_name' => $fileName, 'extension' => $fileExtension, 'size' => $formattedSize]
            : $fileUrl;
    }
}


/**
 * Encrypts data using AES-128-CBC encryption.
 *
 * @param string $data The data to be encrypted.
 * @param string $key The encryption key (optional).
 * @return string The encrypted and encoded string, or an empty string on failure.
 */
function customEncrypt(string|int|null $data, string $key = 'default_secret_key'): string
{
    $cipher = 'AES-128-CBC';
    $iv = substr(md5($key), 0, 16);
    $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);

    if ($encrypted === false) {
        return ''; // or throw an exception depending on your needs
    }

    return rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');
}

function customDecrypt(string|int|null $encryptedData, string $key = 'default_secret_key'): ?string
{
    $cipher = 'AES-128-CBC';
    $iv = substr(md5($key), 0, 16);

    $encryptedData = strtr($encryptedData, '-_', '+/');
    $decoded = base64_decode($encryptedData, true);

    if ($decoded === false) {
        return null; // base64 decode failed
    }

    $decrypted = openssl_decrypt($decoded, $cipher, $key, 0, $iv);

    return $decrypted !== false ? $decrypted : null;
}

function getDefaultCurrencySymbol(): string
{
    $defaultCurrency = GeneralSetting::where('key', 'currency_symbol')->first();
    $currencyId = $defaultCurrency->value ?? '';
    if ($currencyId) {
        $currency = Currency::find((string) $currencyId); // or (int) if it's numeric
        if ($currency instanceof Currency) {
            return $currency->symbol;
        }
    }
    return '$';
}

function isRTL(?string $languageCode = null): bool
{

    $language = TranslationLanguage::select('id')->where('code', $languageCode)->first();
    if ($language) {
        $languageId = $language->id;
        $language = Language::select('rtl')->where('language_id', $languageId)->first();
        if ($language && isset($language->rtl)) {
            return (bool) $language->rtl;
        }
    }
    return 0;
}

if (!function_exists('formatFileSize')) {
    function formatFileSize(int $bytes): string
    {
        $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . ' ' . $sizes[$factor];
    }
}

if (!function_exists('getUserPermissions')) {

    /**
     * @return Collection<int, Permission>
     */
    function getUserPermissions(int|string|null $userId = null): Collection
    {
        $user = $userId ? User::find($userId) : current_user();

        if (!$user || empty($user->role_id)) {
            return collect();
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

function hasPermission(Collection $permissions, $moduleSlug, string $action): bool
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

function current_user(?string $guard = null): ?Authenticatable
{
    $guard = $guard ?? Auth::getDefaultDriver();

    return Auth::guard($guard)->check()
        ? Auth::guard($guard)->user()
        : null;
}
function rentalNotificationEnabled(): int
{
    $bookingNotification = GeneralSetting::where('group_id', 2)->where('key', 'bookingUpdates')->first();
    if ($bookingNotification) {
        return (int) $bookingNotification->value; // Ensure returning an integer value
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
