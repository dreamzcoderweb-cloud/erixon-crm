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
     * Get single setting record or create default instance
     */
    public static function getSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'company_name' => 'PowerGYM',
                'whatsapp_no' => '8610747034',
                'theme_color' => '#00b2a9',
            ]
        );
    }
}
