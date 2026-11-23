<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /**
     * Parse raw duration string (MM:SS, HH:MM:SS, or seconds integer) into integer seconds.
     */
    public static function parseDurationToSeconds($duration): int
    {
        if (empty($duration)) {
            return 0;
        }
        if (is_numeric($duration)) {
            return (int)$duration;
        }
        $parts = explode(':', trim((string)$duration));
        if (count($parts) === 2) {
            return ((int)$parts[0] * 60) + (int)$parts[1];
        } elseif (count($parts) === 3) {
            return ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
        }
        return 0;
    }

    /**
     * Format duration seconds to human-readable string (e.g. "01h 15m 20s" or "02m 45s").
     */
    public static function formatSecondsToHuman(int $seconds): string
    {
        if ($seconds <= 0) {
            return '00m 00s';
        }
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        if ($h > 0) {
            return sprintf('%02dh %02dm %02ds', $h, $m, $s);
        }
        return sprintf('%02dm %02ds', $m, $s);
    }

    /**
     * Retrieve Total Calls summary for a user filtered by period ('day', 'week', 'month', 'year').
     */
    public static function getSummaryForUser($user, string $period = 'day'): array
    {
        $period = strtolower(trim($period));
        $now = Carbon::now();

        switch ($period) {
            case 'week':
            case 'weekly':
                $period = 'week';
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $label = 'This Week (' . $start->format('d M') . ' - ' . $end->format('d M') . ')';
                break;
            case 'month':
            case 'monthly':
                $period = 'month';
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $label = 'This Month (' . $now->format('F Y') . ')';
                break;
            case 'year':
            case 'yearly':
                $period = 'year';
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $label = 'This Year (' . $now->format('Y') . ')';
                break;
            case 'day':
            case 'today':
            case 'daily':
            default:
                $period = 'day';
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Today (' . Carbon::today()->format('d M Y') . ')';
                break;
        }

        $query = self::forUser($user)->where(function ($q) use ($start, $end) {
            $q->whereBetween('call_start_time', [$start, $end])
              ->orWhere(function ($sub) use ($start, $end) {
                  $sub->whereNull('call_start_time')
                      ->whereBetween('created_at', [$start, $end]);
              });
        });

        $calls = $query->get(['call_id', 'call_status', 'call_type', 'duration', 'created_at', 'call_start_time']);
        $totalCalls = $calls->count();
        $answered = 0;
        $missed = 0;
        $busy = 0;
        $totalSeconds = 0;
        $incoming = 0;
        $outgoing = 0;

        foreach ($calls as $c) {
            $st = strtolower(trim((string)$c->call_status));
            if (str_contains($st, 'answer')) {
                $answered++;
            } elseif (str_contains($st, 'busy')) {
                $busy++;
            } else {
                $missed++;
            }

            $type = strtolower(trim((string)$c->call_type));
            if (str_contains($type, 'in')) {
                $incoming++;
            } else {
                $outgoing++;
            }

            $totalSeconds += self::parseDurationToSeconds($c->duration);
        }

        $avgSeconds = $totalCalls > 0 ? (int)round($totalSeconds / $totalCalls) : 0;
        $answerRate = $totalCalls > 0 ? round(($answered / $totalCalls) * 100, 1) : 0;

        return [
            'period'                   => $period,
            'period_label'             => $label,
            'start_date'               => $start->toDateTimeString(),
            'end_date'                 => $end->toDateTimeString(),
            'total_calls'              => $totalCalls,
            'answered_calls'           => $answered,
            'missed_calls'             => $missed,
            'busy_calls'               => $busy,
            'incoming_calls'           => $incoming,
            'outgoing_calls'           => $outgoing,
            'answer_rate'              => $answerRate,
            'total_duration_seconds'   => $totalSeconds,
            'total_duration_formatted' => self::formatSecondsToHuman($totalSeconds),
            'avg_duration_seconds'     => $avgSeconds,
            'avg_duration_formatted'   => self::formatSecondsToHuman($avgSeconds),
        ];
    }
}
