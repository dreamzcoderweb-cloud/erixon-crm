<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadSetting extends Model
{
    use HasFactory;

    protected $table = 'referral_settings';

    protected $fillable = [
        'referral_points',
        'lead_list_columns',
        'customer_list_columns',
        'followup_list_columns',
        'credit_request_list_columns',
    ];

    protected $casts = [
        'lead_list_columns'           => 'array',
        'customer_list_columns'       => 'array',
        'followup_list_columns'       => 'array',
        'credit_request_list_columns' => 'array',
    ];

    /**
     * Get single lead setting record or create default instance
     */
    public static function getSettings()
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'referral_points' => 100,
            ]
        );
    }
}
