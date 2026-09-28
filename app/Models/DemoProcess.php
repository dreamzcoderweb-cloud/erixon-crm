<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoProcess extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'demo_processes';
    protected $primaryKey = 'demo_process_id';

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'lead_source_id',
        'lead_requirement_id',
        'demo_date',
        'demo_time',
        'customer_type',
        'created_by',
        'assigned_by',
        'sub_assigned_by',
        'status',
        'remarks',
        'custom_fields',
    ];

    protected $casts = [
        'demo_date'     => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Relationship to Creator (Sales Staff)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Relationship to Assigned User (Product Manager)
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    /**
     * Relationship to Sub Assigned User (Support Team)
     */
    public function subAssignedUser()
    {
        return $this->belongsTo(User::class, 'sub_assigned_by', 'id');
    }

    /**
     * Relationship to Lead Source
     */
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'lead_sources_id');
    }

    /**
     * Relationship to Lead Requirement
     */
    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    /**
     * User Visibility Scoping (Staff only see their own created demo processes; Admins see all)
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        $userId = $user->id;

        return $query->where('created_by', $userId);
    }

    /**
     * Find if any existing demo conflicts with the requested date and time (within 1 hour / 60 minutes).
     *
     * @param string $demoDate
     * @param string $demoTime
     * @param int|null $excludeId
     * @return DemoProcess|null
     */
    public static function findConflictingSlot(string $demoDate, string $demoTime, ?int $excludeId = null): ?self
    {
        if (empty($demoDate) || empty($demoTime)) {
            return null;
        }

        try {
            $targetDateStr = \Illuminate\Support\Carbon::parse($demoDate)->format('Y-m-d');
            $targetCarbon  = \Illuminate\Support\Carbon::parse($targetDateStr . ' ' . trim($demoTime));
        } catch (\Throwable $e) {
            return null;
        }

        // Query active demo processes around that date (+/- 1 day to cover edge/midnight boundaries)
        $candidates = self::whereBetween('demo_date', [
            \Illuminate\Support\Carbon::parse($targetDateStr)->subDay()->toDateString(),
            \Illuminate\Support\Carbon::parse($targetDateStr)->addDay()->toDateString(),
        ])
        ->when($excludeId, function ($q, $id) {
            $q->where('demo_process_id', '!=', $id);
        })
        ->get();

        foreach ($candidates as $cand) {
            if (empty($cand->demo_time)) {
                continue;
            }

            try {
                $candDateStr = ($cand->demo_date instanceof \DateTimeInterface)
                    ? $cand->demo_date->format('Y-m-d')
                    : \Illuminate\Support\Carbon::parse($cand->demo_date)->format('Y-m-d');
                $candCarbon = \Illuminate\Support\Carbon::parse($candDateStr . ' ' . trim($cand->demo_time));

                $diffMinutes = abs($targetCarbon->diffInMinutes($candCarbon, false));
                if ($diffMinutes < 60) {
                    return $cand;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }
}
