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
