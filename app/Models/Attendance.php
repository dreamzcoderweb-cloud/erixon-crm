<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'permission_start',
        'permission_end',
        'second_check_in',
        'second_check_out',
        'permission_id',
        'working_hours',
        'status',
        'latitude',
        'longitude',
        'second_check_in_latitude',
        'second_check_in_longitude',
        'sessions',
    ];

    protected $casts = [
        'sessions' => 'array',
    ];

    protected $appends = [
        'sessions_list',
        'is_currently_checked_in',
        'current_session_number',
        'active_check_in_time',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function permissionRequest()
    {
        return $this->belongsTo(PermissionRequest::class, 'permission_id', 'id');
    }

    /**
     * Get list of all sessions for this attendance record.
     * Backwards-compatible with legacy check_in/second_check_in fields.
     */
    public function getSessionsListAttribute(): array
    {
        if (!empty($this->sessions) && is_array($this->sessions)) {
            return $this->sessions;
        }

        $list = [];
        if (!empty($this->check_in)) {
            $list[] = [
                'session'   => 1,
                'check_in'  => $this->check_in,
                'check_out' => $this->check_out,
                'latitude'  => $this->latitude,
                'longitude' => $this->longitude,
            ];
        }
        if (!empty($this->second_check_in)) {
            $list[] = [
                'session'   => 2,
                'check_in'  => $this->second_check_in,
                'check_out' => $this->second_check_out,
                'latitude'  => $this->second_check_in_latitude,
                'longitude' => $this->second_check_in_longitude,
            ];
        }

        return $list;
    }

    /**
     * Check if the user is currently checked in (last session has check_in but no check_out).
     */
    public function getIsCurrentlyCheckedInAttribute(): bool
    {
        $sessions = $this->sessions_list;
        if (empty($sessions)) {
            return false;
        }
        $last = end($sessions);
        return !empty($last['check_in']) && empty($last['check_out']);
    }

    /**
     * Get the active or next session number.
     */
    public function getCurrentSessionNumberAttribute(): int
    {
        $sessions = $this->sessions_list;
        if (empty($sessions)) {
            return 1;
        }
        $last = end($sessions);
        if ($this->is_currently_checked_in) {
            return (int) ($last['session'] ?? count($sessions));
        }
        return (int) ($last['session'] ?? count($sessions)) + 1;
    }

    /**
     * Get the check_in time of the currently active session.
     */
    public function getActiveCheckInTimeAttribute(): ?string
    {
        if (!$this->is_currently_checked_in) {
            return null;
        }
        $sessions = $this->sessions_list;
        $last = end($sessions);
        return $last['check_in'] ?? null;
    }
}
