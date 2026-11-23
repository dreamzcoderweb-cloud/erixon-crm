<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

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

    /**
     * Retrieve dynamic chart data for Sale Closed leads filtered by period ('weekly', 'monthly', 'yearly').
     */
    public static function getSaleClosedChartData($user, string $period = 'monthly'): array
    {
        $period = strtolower(trim($period));
        $saleClosedStageId = LeadStage::where(function ($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%sale%close%'])
              ->orWhere('lead_stage_id', 6);
        })->value('lead_stage_id') ?? 6;

        $now = Carbon::now();
        $categories = [];
        $counts = [];
        $revenues = [];

        if (in_array($period, ['week', 'weekly'])) {
            $period = 'weekly';
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            $periodLabel = 'This Week (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';

            $leads = self::forUser($user)
                ->where('lead_stage_id', $saleClosedStageId)
                ->whereBetween('updated_at', [$start, $end])
                ->get(['lead_id', 'expected_amount', 'updated_at', 'created_at']);

            for ($i = 0; $i < 7; $i++) {
                $day = $start->copy()->addDays($i);
                $dayStr = $day->format('Y-m-d');
                $categories[] = $day->format('D (d M)');

                $dayLeads = $leads->filter(function ($l) use ($dayStr) {
                    $dt = $l->updated_at ?? $l->created_at;
                    return $dt && Carbon::parse($dt)->format('Y-m-d') === $dayStr;
                });

                $counts[] = $dayLeads->count();
                $revenues[] = (float)round($dayLeads->sum('expected_amount'), 2);
            }
        } elseif (in_array($period, ['year', 'yearly'])) {
            $period = 'yearly';
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            $periodLabel = 'This Year (' . $now->format('Y') . ')';

            $leads = self::forUser($user)
                ->where('lead_stage_id', $saleClosedStageId)
                ->whereBetween('updated_at', [$start, $end])
                ->get(['lead_id', 'expected_amount', 'updated_at', 'created_at']);

            for ($m = 1; $m <= 12; $m++) {
                $monthDate = Carbon::create($now->year, $m, 1);
                $monthStr = $monthDate->format('Y-m');
                $categories[] = $monthDate->format('M');

                $monthLeads = $leads->filter(function ($l) use ($monthStr) {
                    $dt = $l->updated_at ?? $l->created_at;
                    return $dt && Carbon::parse($dt)->format('Y-m') === $monthStr;
                });

                $counts[] = $monthLeads->count();
                $revenues[] = (float)round($monthLeads->sum('expected_amount'), 2);
            }
        } else {
            // Default: monthly
            $period = 'monthly';
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $periodLabel = 'This Month (' . $now->format('F Y') . ')';

            $leads = self::forUser($user)
                ->where('lead_stage_id', $saleClosedStageId)
                ->whereBetween('updated_at', [$start, $end])
                ->get(['lead_id', 'expected_amount', 'updated_at', 'created_at']);

            // 5 Week Buckets for the current month
            $daysInMonth = $now->daysInMonth;
            $buckets = [
                ['name' => 'Week 1 (1-7)',   'start' => 1,  'end' => 7],
                ['name' => 'Week 2 (8-14)',  'start' => 8,  'end' => 14],
                ['name' => 'Week 3 (15-21)', 'start' => 15, 'end' => 21],
                ['name' => 'Week 4 (22-28)', 'start' => 22, 'end' => 28],
                ['name' => 'Week 5 (29-' . $daysInMonth . ')', 'start' => 29, 'end' => $daysInMonth],
            ];

            foreach ($buckets as $b) {
                $categories[] = $b['name'];
                $bLeads = $leads->filter(function ($l) use ($b, $now) {
                    $dt = $l->updated_at ?? $l->created_at;
                    if (!$dt) return false;
                    $cDate = Carbon::parse($dt);
                    if ($cDate->format('Y-m') !== $now->format('Y-m')) return false;
                    $dayNum = (int)$cDate->format('j');
                    return $dayNum >= $b['start'] && $dayNum <= $b['end'];
                });

                $counts[] = $bLeads->count();
                $revenues[] = (float)round($bLeads->sum('expected_amount'), 2);
            }
        }

        $totalClosedCount = array_sum($counts);
        $totalClosedRevenue = round(array_sum($revenues), 2);
        $avgDealValue = $totalClosedCount > 0 ? round($totalClosedRevenue / $totalClosedCount, 2) : 0;

        return [
            'period'                         => $period,
            'period_label'                   => $periodLabel,
            'categories'                     => $categories,
            'counts'                         => $counts,
            'revenues'                       => $revenues,
            'total_closed_count'             => $totalClosedCount,
            'total_closed_revenue'           => $totalClosedRevenue,
            'total_closed_revenue_formatted' => '₹' . number_format($totalClosedRevenue, 2),
            'avg_deal_value'                 => $avgDealValue,
            'avg_deal_value_formatted'       => '₹' . number_format($avgDealValue, 2),
            'currency'                       => '₹',
        ];
    }
}
