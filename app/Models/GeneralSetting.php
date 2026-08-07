<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo',
        'whatsapp_no',
        'theme_color',
    ];

    /**
     * Cache static settings instance in memory per-request
     */
    protected static $cachedSettings = null;

    /**
     * Get single setting record or create default instance
     */
    public static function getSettings()
    {
        if (static::$cachedSettings !== null) {
            return static::$cachedSettings;
        }

        try {
            $setting = static::first();
            if (!$setting) {
                $setting = static::create([
                    'company_name' => 'Erixon CRM',
                    'logo' => null,
                    'whatsapp_no' => null,
                    'theme_color' => '#00b2a9',
                ]);
            }
            static::$cachedSettings = $setting;
            return $setting;
        } catch (\Throwable $e) {
            return new static([
                'company_name' => 'Erixon CRM',
                'logo' => null,
                'whatsapp_no' => null,
                'theme_color' => '#00b2a9',
            ]);
        }
    }

    /**
     * Clear cached setting instance
     */
    public static function clearCache()
    {
        static::$cachedSettings = null;
    }
}
