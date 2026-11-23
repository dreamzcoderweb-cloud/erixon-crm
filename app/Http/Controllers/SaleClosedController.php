<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Lead;
use App\Models\LeadRequirement;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleClosedController extends Controller
{
    /**
     * Resolve the Sale Closed stage ID dynamically.
     */
    protected function getSaleClosedStageId(): int
    {
        $id = LeadStage::where(function ($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%sale%close%'])
              ->orWhere('lead_stage_id', 6);
        })->value('lead_stage_id');

        return $id ? (int)$id : 6;
    }

    /**
     * Display the Sale Closed leads page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('sale-closed.view') && !$user->can('leads.view') && !$user->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        // Branches, Teams, and Staffs scoped by user permissions
        if ($user->isAdmin()) {
            $branches = Branch::where('status', 1)->orderBy('name')->get();
            $teams    = Team::where('status', 1)->orderBy('name')->get();
            $staffs   = User::staffOnly()->where('status', 1)->orderBy('name')->get();
        } elseif ($user->isBranchManager()) {
            $branchIds = $user->accessibleBranchIds() ?? [];
            $branches  = Branch::whereIn('id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $teams     = Team::whereIn('branch_id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $staffs    = User::staffOnly()->whereIn('branch_id', $branchIds)->where('status', 1)->orderBy('name')->get();
        } elseif ($user->isTeamLeader()) {
            $teamIds   = $user->accessibleTeamIds() ?? [];
            $branchIds = $user->accessibleBranchIds() ?? [];
            $branches  = Branch::whereIn('id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $teams     = Team::whereIn('id', $teamIds)->where('status', 1)->orderBy('name')->get();
            $staffs    = User::staffOnly()->whereIn('team_id', $teamIds)->where('status', 1)->orderBy('name')->get();
        } else {
            $branches = $user->branch_id ? Branch::where('id', $user->branch_id)->get() : collect();
            $teams    = $user->team_id ? Team::where('id', $user->team_id)->get() : collect();
            $staffs   = collect([$user]);
        }

        $leadRequirements = LeadRequirement::where('status', 1)->orderBy('name')->get();
        $leadSources      = LeadSource::where('status', 1)->orderBy('name')->get();

        // Year options for the yearly filter dropdown (current year down to 2023)
        $currentYear = (int)date('Y');
        $years = range($currentYear, max(2023, $currentYear - 4));

        return view('sale_closed.view', compact(
            'branches',
            'teams',
            'staffs',
            'leadRequirements',
            'leadSources',
            'years'
        ));
    }

    /**
     * Return JSON data for DataTables and KPI summary.
     */
    public function listData(Request $request)
    {
        $user = Auth::user();
        if (!$user->can('sale-closed.view') && !$user->can('leads.view') && !$user->isAdmin()) {
            return response()->json(['status' => false, 'message' => 'Unauthorized.'], 403);
        }

        $saleClosedStageId = $this->getSaleClosedStageId();

        $query = Lead::forUser($user)->where('lead_stage_id', $saleClosedStageId);

        // Branch filter (supports primary column and pivot branch_lead)
        if ($request->filled('branch_id')) {
            $bId = $request->input('branch_id');
            $query->where(function ($q) use ($bId) {
                $q->where('leads.branch_id', $bId)
                  ->orWhereHas('branches', function ($bq) use ($bId) {
                      $bq->where('branches.id', $bId);
                  });
            });
        }

        // Team filter (supports primary column and pivot lead_team)
        if ($request->filled('team_id')) {
            $tId = $request->input('team_id');
            $query->where(function ($q) use ($tId) {
                $q->where('leads.team_id', $tId)
                  ->orWhereHas('teams', function ($tq) use ($tId) {
                      $tq->where('teams.id', $tId);
                  });
            });
        }

        // Assigned to (staff) filter
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        // Requirement filter
        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // Source filter
        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->input('lead_source_id'));
        }

        // Created by filter
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        // Period filter (Daily, Monthly, Yearly, Custom, All)
        $filterType = $request->input('filter_type', 'all');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $year       = $request->input('year');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$selYear, $selMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $selYear ?: date('Y'))
                  ->whereMonth('created_at', $selMonth ?: date('m'));
        } elseif ($filterType === 'yearly' && !empty($year)) {
            $query->whereYear('created_at', $year);
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Eager load relations
        $leads = (clone $query)->with([
            'branch:id,name,code',
            'team:id,name,code',
            'branches:id,name,code',
            'teams:id,name,code',
            'customer:customer_id,customer_type,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'assignedUser:id,name,email',
            'creator:id,name'
        ])->orderBy('lead_id', 'DESC')->get();

        // Calculate KPI summary for Sale Closed leads
        $baseKpiQuery = Lead::forUser($user)->where('lead_stage_id', $saleClosedStageId);
        if ($request->filled('branch_id')) {
            $bId = $request->input('branch_id');
            $baseKpiQuery->where(function ($q) use ($bId) {
                $q->where('leads.branch_id', $bId)
                  ->orWhereHas('branches', function ($bq) use ($bId) {
                      $bq->where('branches.id', $bId);
                  });
            });
        }
        if ($request->filled('team_id')) {
            $tId = $request->input('team_id');
            $baseKpiQuery->where(function ($q) use ($tId) {
                $q->where('leads.team_id', $tId)
                  ->orWhereHas('teams', function ($tq) use ($tId) {
                      $tq->where('teams.id', $tId);
                  });
            });
        }

        $todayStr = Carbon::today()->toDateString();
        $currYear = Carbon::now()->year;
        $currMonth = Carbon::now()->month;

        $totalCount  = (clone $baseKpiQuery)->count();
        $totalAmount = (clone $baseKpiQuery)->sum('expected_amount') ?: 0;

        $todayCount  = (clone $baseKpiQuery)->whereDate('created_at', $todayStr)->count();
        $todayAmount = (clone $baseKpiQuery)->whereDate('created_at', $todayStr)->sum('expected_amount') ?: 0;

        $monthCount  = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->whereMonth('created_at', $currMonth)->count();
        $monthAmount = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->whereMonth('created_at', $currMonth)->sum('expected_amount') ?: 0;

        $yearCount   = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->count();
        $yearAmount  = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->sum('expected_amount') ?: 0;

        $filteredCount  = $leads->count();
        $filteredAmount = $leads->sum('expected_amount') ?: 0;

        return response()->json([
            'status' => true,
            'data'   => $leads,
            'kpi'    => [
                'total_count'     => $totalCount,
                'total_amount'    => number_format((float)$totalAmount, 2),
                'today_count'     => $todayCount,
                'today_amount'    => number_format((float)$todayAmount, 2),
                'month_count'     => $monthCount,
                'month_amount'    => number_format((float)$monthAmount, 2),
                'year_count'      => $yearCount,
                'year_amount'     => number_format((float)$yearAmount, 2),
                'filtered_count'  => $filteredCount,
                'filtered_amount' => number_format((float)$filteredAmount, 2),
            ],
        ]);
    }
}
