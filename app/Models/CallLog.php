<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $table = 'call_logs';
    protected $primaryKey = 'call_id';

    public const UPDATED_AT = null;

    protected $fillable = [
        'lead_id',
        'customer_id',
        'customer_code',
        'customer_name',
        'followup_id',
        'followup_code',
        'followup_date',
        'user_id',
        'phone',
        'call_type',
        'call_source',
        'duration',
        'call_start_time',
        'call_end_time',
        'call_status',
        'notes',
        'recording_id',
        'recording_file',
        'created_at',
    ];

    protected $casts = [
        'call_start_time' => 'datetime',
        'call_end_time'   => 'datetime',
        'followup_date'   => 'date',
        'created_at'      => 'datetime',
    ];

    protected $appends = [
        'recording_url',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function followup()
    {
        return $this->belongsTo(Followup::class, 'followup_id', 'followups_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function recording()
    {
        return $this->belongsTo(CallRecording::class, 'recording_id', 'call_id');
    }

    public function getRecordingUrlAttribute(): ?string
    {
        $file = $this->recording_file ?? $this->recording?->recording_file;
        if (empty($file)) {
            return null;
        }

        return get_media_url($file);
    }

    /**
     * Scope call logs accessible by a specific user (staff-wise data access).
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        $userId = $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhereHas('lead', function ($lq) use ($userId) {
                  $lq->where('assigned_to', $userId)
                    ->orWhere('created_by', $userId);
              });
        });
    }
}
