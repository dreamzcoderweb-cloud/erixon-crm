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

        return $setting->{$key} ?? $default;
    }
}

if (!function_exists('company_name')) {
    /**
     * Get company name setting
     *
     * @param string $default
     * @return string
     */
    function company_name($default = 'Erixon CRM')
    {
        return general_setting('company_name', $default);
    }
}

if (!function_exists('company_logo')) {
    /**
     * Get company logo asset URL or fallback
     *
     * @param string|null $default
     * @return string|null
     */
    function company_logo($default = null)
    {
        $logo = general_setting('logo');
        if (!empty($logo) && file_exists(public_path($logo))) {
            return asset($logo);
        }
        return $default;
    }
}

if (!function_exists('company_favicon')) {
    /**
     * Get company favicon asset URL or fallback
     *
     * @param string|null $default
     * @return string|null
     */
    function company_favicon($default = null)
    {
        $favicon = general_setting('favicon');
        if (!empty($favicon) && file_exists(public_path($favicon))) {
            return asset($favicon);
        }
        return $default ?: asset('assets/img/fav_icon.png');
    }
}

if (!function_exists('theme_color')) {
    /**
     * Get theme color setting
     *
     * @param string $default
     * @return string
     */
    function theme_color($default = '#00b2a9')
    {
        return general_setting('theme_color', $default);
    }
}

if (!function_exists('whatsapp_no')) {
    /**
     * Get WhatsApp number setting
     *
     * @param string|null $default
     * @return string|null
     */
    function whatsapp_no($default = null)
    {
        return general_setting('whatsapp_no', $default);
    }
}

if (!function_exists('upload_file')) {
    /**
     * Upload a file using FileUploadService
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $category
     * @param string|null $oldFilePath
     * @param string|null $customPrefix
     * @return string
     */
    function upload_file(\Illuminate\Http\UploadedFile $file, string $category = 'documents', ?string $oldFilePath = null, ?string $customPrefix = null): string
    {
        return \App\Services\FileUploadService::upload($file, $category, $oldFilePath, $customPrefix);
    }
}

if (!function_exists('delete_file')) {
    /**
     * Delete a file using FileUploadService
     *
     * @param string|null $filePath
     * @return bool
     */
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
