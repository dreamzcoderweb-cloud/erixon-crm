<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_image',
        'password',
        'mobile_number',
        'address',
        'is_on_leave',
        'gender',
        'date_of_birth',
        'date_of_joining',
        'designation',
        'base_salary',
        'available_leave_count',
        'check_in_time',
        'check_out_time',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_on_leave' => 'boolean',
            'date_of_birth' => 'date',
            'date_of_joining' => 'date',
            'base_salary' => 'decimal:2',
            'available_leave_count' => 'float',
        ];
    }

    /**
     * Scope to filter active staff not on leave
     */
    public function scopeAvailableForAssignment($query)
    {
        return $query->where('is_on_leave', false);
    }

    /**
     * Get leave requests for user
     */
    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class, 'user_id', 'id');
    }

    /**
     * Get profile image URL or fallback default avatar
     *
     * @return string
     */
    public function getProfileImageUrlAttribute(): string
    {
        if (!empty($this->profile_image) && file_exists(public_path($this->profile_image))) {
            return asset($this->profile_image);
        }

        return asset('assets/img/avatars/1.png');
    }
}
