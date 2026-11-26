<?php

use App\Models\GeneralSetting;

if (!function_exists('general_setting')) {
    /**
     * Get general setting instance or a specific attribute value
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function general_setting($key = null, $default = null)
{
    $setting = GeneralSetting::getSettings();

    if (is_null($key)) {
        return $setting;
    }

    $value = $setting->{$key} ?? $default;

    if (in_array($key, ['logo', 'favicon', 'proposal_logo']) && !empty($value)) {
        $value = 'public/' . ltrim(str_replace('public/', '', $value), '/');
    }

    return $value;
}
}

if (!function_exists('company_name')) {
    function company_name($default = 'Erixon CRM')
    {
        return general_setting('company_name', $default);
    }
}

if (!function_exists('company_logo')) {
    function company_logo($default = null)
{
    $logo = general_setting('logo');

    if (!empty($logo)) {
        $filePath = public_path(str_replace('public/', '', $logo));

        if (file_exists($filePath)) {
            return asset($logo);
        }
    }

    return $default;
}
}

if (!function_exists('company_favicon')) {
    function company_favicon($default = null)
    {
        $favicon = general_setting('favicon');

        if (!empty($favicon)) {
            $filePath = public_path(str_replace('public/', '', $favicon));

            if (file_exists($filePath)) {
                return asset($favicon);
            }
        }

        return $default ?: asset('public/assets/img/fav_icon.png');
    }
}
if (!function_exists('proposal_logo')) {
    function proposal_logo($default = null)
    {
        $logo = general_setting('proposal_logo');

        if (!empty($logo)) {
            $filePath = public_path(str_replace('public/', '', $logo));

            if (file_exists($filePath)) {
                return asset($logo);
            }
        }

        return $default;
    }
}
if (!function_exists('theme_color')) {
    function theme_color($default = '#00b2a9')
    {
        return general_setting('theme_color', $default);
    }
}

if (!function_exists('whatsapp_no')) {
    function whatsapp_no($default = null)
    {
        return general_setting('whatsapp_no', $default);
    }
}

if (!function_exists('upload_file')) {
    function upload_file(
        \Illuminate\Http\UploadedFile $file,
        string $category = 'documents',
        ?string $oldFilePath = null,
        ?string $customPrefix = null
    ): string {
        return \App\Services\FileUploadService::upload(
            $file,
            $category,
            $oldFilePath,
            $customPrefix
        );
    }
}

if (!function_exists('delete_file')) {
    function delete_file(?string $filePath): bool
    {
        return \App\Services\FileUploadService::delete($filePath);
    }
}


if (!function_exists('get_media_url')) {
    /**
     * Get accessible URL for uploaded media file (handles both local dev and live server subdirectories).
     *
     * @param string|null $filePath
     * @return string|null
     */
    function get_media_url(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        if (filter_var($filePath, FILTER_VALIDATE_URL)) {
            return $filePath;
        }

        $cleanPath = ltrim(str_replace('\\', '/', $filePath), '/');

        // Check if environment is live server or base URL has subfolder without /public
        $appUrl = (string) config('app.url', '');
        $requestRoot = '';
        $requestHost = '';
        try {
            if (app()->bound('request') && request()) {
                $requestRoot = (string) request()->root();
                $requestHost = (string) request()->getHost();
            }
        } catch (\Throwable $e) {
            // CLI fallback
        }

        $isLiveOrSubdir = str_contains($requestHost, 'erixon.in')
            || str_contains($requestRoot, 'erixon.in')
            || str_contains($appUrl, 'erixon.in')
            || (str_contains($requestRoot, '/crm') && !str_ends_with(rtrim($requestRoot, '/'), '/public'))
            || (str_contains($appUrl, '/crm') && !str_ends_with(rtrim($appUrl, '/'), '/public'));

        if ($isLiveOrSubdir) {
            if (!str_starts_with($cleanPath, 'public/')) {
                $cleanPath = 'public/' . $cleanPath;
            }
        }

        return asset($cleanPath);
    }
}
