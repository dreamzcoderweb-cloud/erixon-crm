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
