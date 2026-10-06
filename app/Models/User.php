<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, Auditable;

    protected $auditModule = 'Staff';

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
        'staff_type',
        'base_salary',
        'available_leave_count',
        'check_in_time',
        'allow_check_in_time',
        'late_attendance_count',
        'increment_amount',
        'increment_date',
        'check_out_time',
        'status',
        'fcmtoken',
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
            'late_attendance_count' => 'integer',
            'increment_amount' => 'decimal:2',
            'increment_date' => 'date',
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
     * Scope to exclude primary Super Admin account (ID 1) and return staff members only
     */
    public function scopeStaffOnly($query)
    {
        return $query->where('users.id', '!=', 1);
    }

    /**
     * Check if user is an Admin or Super Admin
     */
    public function isAdmin(): bool
    {
        if ($this->id === 1) {
            return true;
        }

        return $this->hasAnyRole([
            'Super Admin',
            'Admin',
            'super admin',
            'super-admin',
            'Super-Admin',
            'admin',
        ]);
    }

    /**
     * Check if user is a Super Admin
     */
    public function isSuperAdmin(): bool
    {
        if ($this->id === 1) {
            return true;
        }

        return $this->hasAnyRole([
            'Super Admin',
            'super admin',
            'super-admin',
            'Super-Admin',
        ]);
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

    /**
     * Get today's answered calls count for the user.
     */
    public function getTodayAnsweredCallsCount(): int
    {
        $today = \Carbon\Carbon::today()->toDateString();

        return \App\Models\CallLog::where('user_id', $this->id)
            ->where('call_status', 'Answered')
            ->where(function ($q) use ($today) {
                $q->whereDate('call_start_time', $today)
                  ->orWhere(function ($q2) use ($today) {
                      $q2->whereNull('call_start_time')
                         ->whereDate('created_at', $today);
                  });
            })
            ->count();
    }

    /**
     * Check if user is a Sales Manager
     */
    public function isSalesManager(): bool
    {
        if ($this->hasAnyRole([
            'Sales Manager',
            'sales manager',
            'Sales manager',
            'sales_manager',
            'Sales-Manager',
            'sales-manager',
        ])) {
            return true;
        }

        $roleName = $this->roles?->first()?->name ?? $this->getRoleNames()->first();
        if ($roleName && strcasecmp(trim(str_replace(['_', '-'], ' ', $roleName)), 'Sales Manager') === 0) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is eligible to check out based on required answered calls (min 50 answered calls per day mandatory ONLY for Sales Manager role).
     * Super Admin, Admin, and all other staff roles are exempt and can always check out freely.
     */
    public function canCheckOut(): array
    {
        $completed = $this->getTodayAnsweredCallsCount();

        // Super Admin and Admin are always exempt
        if ($this->isAdmin()) {
            return [
                'allowed'   => true,
                'completed' => $completed,
                'required'  => 0,
                'remaining' => 0,
                'message'   => 'Admin and Super Admin are exempt from call requirements.',
            ];
        }

        // 50 answered calls per day is mandatory ONLY for Sales Manager role. All other roles are exempt.
        if (!$this->isSalesManager()) {
            return [
                'allowed'   => true,
                'completed' => $completed,
                'required'  => 0,
                'remaining' => 0,
                'message'   => 'Check-out allowed. 50 answered calls requirement is only mandatory for Sales Manager.',
            ];
        }

        $required = 50;
        $remaining = max(0, $required - $completed);

        return [
            'allowed'   => $completed >= $required,
            'completed' => $completed,
            'required'  => $required,
            'remaining' => $remaining,
            'message'   => $completed >= $required
                ? 'Eligible to check out.'
                : "Cannot check out. Sales Manager must complete at least {$required} answered calls today before checking out. You have completed {$completed} answered calls ({$remaining} remaining).",
        ];
    }

    /**
     * Check if user is eligible to log out.
     * Call requirement has been replaced to attendance check-out; logout is now unrestricted.
     */
    public function canLogout(): array
    {
        $completed = $this->getTodayAnsweredCallsCount();

        return [
            'allowed'   => true,
            'completed' => $completed,
            'required'  => 0,
            'remaining' => 0,
            'message'   => 'Logout allowed.',
        ];
    }
}
