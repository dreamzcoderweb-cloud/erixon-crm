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

    protected static function booted()
    {
        static::deleting(function ($user) {
            if (!$user->isForceDeleting() && !empty($user->email) && !str_contains($user->email, '_d')) {
                $suffix = '_d' . $user->id;
                $maxLen = 191 - strlen($suffix);
                $user->email = substr($user->email, 0, $maxLen) . $suffix;
                $user->saveQuietly();
            }
        });
    }

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
        'branch_id',
        'team_id',
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
     * Check if user is a staff member
     */
    public function isStaff(): bool
    {
        return $this->id != 1 && !$this->isSuperAdmin();
    }

    /**
     * Check if user has HR designation or role
     */
    public function isHr(): bool
    {
        $des = strtolower(trim((string)$this->designation));
        if (str_contains($des, 'hr')) {
            return true;
        }

        return $this->hasAnyRole(['HR', 'hr', 'Hr', 'HR Manager', 'hr manager', 'HR / Admin']);
    }

    /**
     * Check if user has MD designation or is Super Admin
     */
    public function isMd(): bool
    {
        $des = strtolower(trim((string)$this->designation));
        if (str_contains($des, 'md') || str_contains($des, 'managing director')) {
            return true;
        }

        return $this->id === 1 || $this->isSuperAdmin();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_user')->withPivot('is_primary')->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')->withPivot('role_in_team', 'is_primary')->withTimestamps();
    }

    /**
     * Check if user is a Branch Manager
     */
    public function isBranchManager(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return Branch::where('manager_id', $this->id)->exists()
            || $this->hasAnyRole(['Branch Manager', 'branch manager', 'Branch-Manager']);
    }

    /**
     * Check if user is a Team Leader
     */
    public function isTeamLeader(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        return Team::where('leader_id', $this->id)->exists()
            || $this->hasAnyRole(['Team Leader', 'team leader', 'Team-Leader', 'Team Lead', 'team lead']);
    }

    /**
     * Get accessible branch IDs for the user.
     * Returns null if unrestricted (Super Admin / Admin).
     *
     * @return array<int>|null
     */
    public function accessibleBranchIds(): ?array
    {
        if ($this->isAdmin()) {
            return null; // All branches
        }

        $ids = [];
        if (!empty($this->branch_id)) {
            $ids[] = (int) $this->branch_id;
        }

        $managedIds = Branch::where('manager_id', $this->id)->pluck('id')->all();
        $ids = array_merge($ids, $managedIds);

        $pivotIds = \Illuminate\Support\Facades\DB::table('branch_user')->where('user_id', $this->id)->pluck('branch_id')->all();
        $ids = array_merge($ids, $pivotIds);

        return array_values(array_unique(array_filter($ids)));
    }

    /**
     * Get accessible team IDs for the user.
     * Returns null if unrestricted (Super Admin / Admin).
     *
     * @return array<int>|null
     */
    public function accessibleTeamIds(): ?array
    {
        if ($this->isAdmin()) {
            return null; // All teams
        }

        if ($this->isBranchManager()) {
            $branchIds = $this->accessibleBranchIds();
            if (!empty($branchIds)) {
                return Team::whereIn('branch_id', $branchIds)->pluck('id')->all();
            }
        }

        $ids = [];
        if (!empty($this->team_id)) {
            $ids[] = (int) $this->team_id;
        }

        $ledIds = Team::where('leader_id', $this->id)->pluck('id')->all();
        $ids = array_merge($ids, $ledIds);

        $pivotIds = \Illuminate\Support\Facades\DB::table('team_user')->where('user_id', $this->id)->pluck('team_id')->all();
        $ids = array_merge($ids, $pivotIds);

        return array_values(array_unique(array_filter($ids)));
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
