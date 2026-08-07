<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_points',
    ];

    /**
     * Get single referral setting record or create default instance
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
