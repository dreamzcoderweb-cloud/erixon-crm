<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'leads';
    protected $primaryKey = 'lead_id';

    protected $fillable = [
        'customer_id',
        'branch_id',
        'team_id',
        'customer_name',
        'customer_type',
        'mobile',
        'email',
        'lead_title',
        'lead_source_id',
        'lead_stage_id',
        'lead_requirement_id',
        'assigned_to',
        'priority',
        'expected_amount',
        'description',
        'custom_fields',
        'next_followup_date',
        'status',
        'lost_reason_id',
        'created_by',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    protected static function booted()
    {
        static::deleting(function ($lead) {
            // Delete associated lead documents and physical files
            foreach ($lead->documents as $doc) {
                $doc->delete();
            }

            // Delete associated call recordings and physical audio files
            foreach ($lead->callRecordings as $rec) {
                $rec->delete();
            }

            foreach ($lead->callLogs as $log) {
                $log->delete();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'lead_sources_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id', 'lead_stage_id');
    }

    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    public function lostReason()
    {
        return $this->belongsTo(LostReason::class, 'lost_reason_id', 'lost_reason_id');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class, 'lead_id', 'lead_id');
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class, 'lead_id', 'lead_id');
    }

    public function callRecordings()
    {
        return $this->hasMany(CallRecording::class, 'lead_id', 'lead_id');
    }

    public function callLogs()
    {
        return $this->hasMany(CallLog::class, 'lead_id', 'lead_id');
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
        return $this->belongsToMany(Branch::class, 'branch_lead', 'lead_id', 'branch_id')->withPivot('is_primary')->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'lead_team', 'lead_id', 'team_id')->withPivot('is_primary')->withTimestamps();
    }

    /**
     * Scope leads accessible by a specific user (staff-wise data access).
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        // Branch Manager scope: sees all leads in their branch(es)
        if ($user->isBranchManager()) {
            $branchIds = $user->accessibleBranchIds();
            if (!empty($branchIds)) {
                return $query->where(function ($q) use ($branchIds) {
                    $q->whereIn('leads.branch_id', $branchIds)
                      ->orWhereHas('branches', fn($bq) => $bq->whereIn('branches.id', $branchIds));
                });
            }
        }

        // Team Leader scope: sees all leads in their team(s)
        if ($user->isTeamLeader()) {
            $teamIds = $user->accessibleTeamIds();
            if (!empty($teamIds)) {
                return $query->where(function ($q) use ($teamIds) {
                    $q->whereIn('leads.team_id', $teamIds)
                      ->orWhereHas('teams', fn($tq) => $tq->whereIn('teams.id', $teamIds));
                });
            }
        }

        // Staff scope: own leads (assigned, created, or followups forwarded)
        $userId = $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('leads.assigned_to', $userId)
              ->orWhere('leads.created_by', $userId)
              ->orWhereHas('followups', function ($fq) use ($userId) {
                  $fq->where('forward_to', $userId)
                    ->orWhere('created_by', $userId);
              });
        });
    }
}
