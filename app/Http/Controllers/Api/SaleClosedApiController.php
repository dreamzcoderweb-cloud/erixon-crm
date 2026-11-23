<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Lead;
use App\Models\LeadRequirement;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\Team;
use App\Models\User;
use App\Traits\HasApiPermissionCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleClosedApiController extends Controller
{
    use HasApiPermissionCheck;

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
     * Filter and retrieve Sale Closed leads report with KPI metrics.
     * Supports daily, monthly, yearly, custom period filters, multi-branch, and multi-team.
     *
     * Accessible via GET api/v1/reports/sale-closed or GET api/v1/sale-closed/report
     */
    public function report(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($user, 'sale-closed.view') && !$this->hasPermission($user, 'leads.view') && !$user->isStaff()) {
            return $this->permissionDeniedResponse('Sale Closed reports');
        }

        $saleClosedStageId = $this->getSaleClosedStageId();

        // Base scoped query for current user role
        $query = Lead::forUser($user)->where('lead_stage_id', $saleClosedStageId);

        // 1. Period filter (Daily, Monthly, Yearly, Custom, All)
        $filterType = strtolower(trim((string)$request->input('filter_type', 'all')));
        $date       = $request->input('date');
        $month      = $request->input('month');
        $year       = $request->input('year');
        $startDate  = $request->input('start_date', $request->input('from_date', $request->input('fromDate')));
        $endDate    = $request->input('end_date', $request->input('to_date', $request->input('toDate')));

        if ($filterType === 'daily') {
            $targetDate = !empty($date) ? $date : Carbon::today()->toDateString();
            $query->whereDate('created_at', $targetDate);
        } elseif ($filterType === 'monthly') {
            $targetMonth = !empty($month) ? $month : Carbon::now()->format('Y-m');
            [$selYear, $selMonth] = array_pad(explode('-', $targetMonth), 2, null);
            $query->whereYear('created_at', $selYear ?: date('Y'))
                  ->whereMonth('created_at', $selMonth ?: date('m'));
        } elseif ($filterType === 'yearly') {
            $targetYear = !empty($year) ? (int)$year : (int)date('Y');
            $query->whereYear('created_at', $targetYear);
        } elseif ($filterType === 'custom') {
            if (!empty($startDate) && !empty($endDate)) {
                try {
                    $query->whereBetween('created_at', [
                        Carbon::parse(trim((string)$startDate))->startOfDay(),
                        Carbon::parse(trim((string)$endDate))->endOfDay()
                    ]);
                } catch (\Throwable $e) {
                    $query->whereDate('created_at', '>=', $startDate)
                          ->whereDate('created_at', '<=', $endDate);
                }
            } elseif (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // 2. Branch filter (supports primary branch_id column and pivot branch_lead)
        if ($request->filled('branch_id') || $request->filled('branch_ids')) {
            $rawB = $request->input('branch_ids', $request->input('branch_id'));
            $bList = is_array($rawB) ? $rawB : explode(',', (string)$rawB);
            $bList = array_values(array_filter(array_map('intval', $bList)));
            if (!empty($bList)) {
                $query->where(function ($bq) use ($bList) {
                    $bq->whereIn('leads.branch_id', $bList)
                       ->orWhereHas('branches', function ($pbq) use ($bList) {
                           $pbq->whereIn('branches.id', $bList);
                       });
                });
            }
        }

        // 3. Team filter (supports primary team_id column and pivot lead_team)
        if ($request->filled('team_id') || $request->filled('team_ids')) {
            $rawT = $request->input('team_ids', $request->input('team_id'));
            $tList = is_array($rawT) ? $rawT : explode(',', (string)$rawT);
            $tList = array_values(array_filter(array_map('intval', $tList)));
            if (!empty($tList)) {
                $query->where(function ($tq) use ($tList) {
                    $tq->whereIn('leads.team_id', $tList)
                       ->orWhereHas('teams', function ($ptq) use ($tList) {
                           $ptq->whereIn('teams.id', $tList);
                       });
                });
            }
        }

        // 4. Assigned staff filter
        if ($request->filled('assigned_to')) {
            $query->where('leads.assigned_to', $request->input('assigned_to'));
        }

        // 5. Requirement filter
        if ($request->filled('lead_requirement_id')) {
            $query->where('leads.lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // 6. Source filter
        if ($request->filled('lead_source_id')) {
            $query->where('leads.lead_source_id', $request->input('lead_source_id'));
        }

        // 7. Search filter (customer name, mobile, email, lead title)
        if ($request->filled('search')) {
            $search = trim((string)$request->input('search'));
            $query->where(function ($sq) use ($search) {
                $sq->where('leads.lead_title', 'like', "%{$search}%")
                   ->orWhere('leads.customer_name', 'like', "%{$search}%")
                   ->orWhere('leads.mobile', 'like', "%{$search}%")
                   ->orWhere('leads.email', 'like', "%{$search}%")
                   ->orWhereHas('customer', function ($cq) use ($search) {
                       $cq->where('name', 'like', "%{$search}%")
                          ->orWhere('mobile', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                   });
            });
        }

        // Calculate KPI summary for Sale Closed leads
        $baseKpiQuery = Lead::forUser($user)->where('lead_stage_id', $saleClosedStageId);
        if ($request->filled('branch_id') || $request->filled('branch_ids')) {
            $rawB = $request->input('branch_ids', $request->input('branch_id'));
            $bList = is_array($rawB) ? $rawB : explode(',', (string)$rawB);
            $bList = array_values(array_filter(array_map('intval', $bList)));
            if (!empty($bList)) {
                $baseKpiQuery->where(function ($bq) use ($bList) {
                    $bq->whereIn('leads.branch_id', $bList)
                       ->orWhereHas('branches', function ($pbq) use ($bList) {
                           $pbq->whereIn('branches.id', $bList);
                       });
                });
            }
        }
        if ($request->filled('team_id') || $request->filled('team_ids')) {
            $rawT = $request->input('team_ids', $request->input('team_id'));
            $tList = is_array($rawT) ? $rawT : explode(',', (string)$rawT);
            $tList = array_values(array_filter(array_map('intval', $tList)));
            if (!empty($tList)) {
                $baseKpiQuery->where(function ($tq) use ($tList) {
                    $tq->whereIn('leads.team_id', $tList)
                       ->orWhereHas('teams', function ($ptq) use ($tList) {
                           $ptq->whereIn('teams.id', $tList);
                       });
                });
            }
        }

        $todayStr  = Carbon::today()->toDateString();
        $currYear  = Carbon::now()->year;
        $currMonth = Carbon::now()->month;

        $totalCount  = (clone $baseKpiQuery)->count();
        $totalAmount = (float) ((clone $baseKpiQuery)->sum('expected_amount') ?: 0);

        $todayCount  = (clone $baseKpiQuery)->whereDate('created_at', $todayStr)->count();
        $todayAmount = (float) ((clone $baseKpiQuery)->whereDate('created_at', $todayStr)->sum('expected_amount') ?: 0);

        $monthCount  = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->whereMonth('created_at', $currMonth)->count();
        $monthAmount = (float) ((clone $baseKpiQuery)->whereYear('created_at', $currYear)->whereMonth('created_at', $currMonth)->sum('expected_amount') ?: 0);

        $yearCount   = (clone $baseKpiQuery)->whereYear('created_at', $currYear)->count();
        $yearAmount  = (float) ((clone $baseKpiQuery)->whereYear('created_at', $currYear)->sum('expected_amount') ?: 0);

        $filteredCount  = (clone $query)->count();
        $filteredAmount = (float) ((clone $query)->sum('expected_amount') ?: 0);

        // Fetch query results
        $queryWithRelations = $query->with([
            'customer:customer_id,name,mobile,email,customer_type',
            'branch:id,name,code',
            'team:id,name,code',
            'branches:id,name,code',
            'teams:id,name,code',
            'leadRequirement:lead_requirements_id,name',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ])->orderBy('lead_id', 'desc');

        $perPage = (int) $request->input('per_page', 20);
        $returnAll = in_array(strtolower((string)$request->input('all', '')), ['1', 'true', 'yes'], true);

        if ($returnAll) {
            $leadsList = $queryWithRelations->get();
            $pagination = null;
        } else {
            $paginated = $queryWithRelations->paginate($perPage);
            $leadsList = collect($paginated->items());
            $pagination = [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
            ];
        }

        // Format lead items with multiple branches and teams
        $formattedLeads = $leadsList->map(function ($lead) {
            $branchIds = $lead->branches->pluck('id')->values()->all();
            if (empty($branchIds) && !empty($lead->branch_id)) {
                $branchIds = [(int)$lead->branch_id];
            }

            $teamIds = $lead->teams->pluck('id')->values()->all();
            if (empty($teamIds) && !empty($lead->team_id)) {
                $teamIds = [(int)$lead->team_id];
            }

            $amount = (float) ($lead->expected_amount ?: 0);

            return [
                'lead_id'             => $lead->lead_id,
                'lead_title'          => $lead->lead_title,
                'customer_id'         => $lead->customer_id,
                'customer_name'       => $lead->customer_name ?: ($lead->customer ? $lead->customer->name : null),
                'customer_type'       => $lead->customer_type ?: ($lead->customer ? $lead->customer->customer_type : 'user'),
                'mobile'              => $lead->mobile ?: ($lead->customer ? $lead->customer->mobile : null),
                'email'               => $lead->email ?: ($lead->customer ? $lead->customer->email : null),
                'expected_amount'     => $amount,
                'expected_amount_fmt' => '₹' . number_format($amount, 2),
                'branch_id'           => $lead->branch_id,
                'team_id'             => $lead->team_id,
                'branch_ids'          => $branchIds,
                'team_ids'            => $teamIds,
                'branches'            => $lead->branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'code' => $b->code]),
                'teams'               => $lead->teams->map(fn($t) => ['id' => $t->id, 'name' => $t->name, 'code' => $t->code]),
                'lead_requirement'    => $lead->leadRequirement ? ['id' => $lead->leadRequirement->lead_requirements_id, 'name' => $lead->leadRequirement->name] : null,
                'lead_source'         => $lead->leadSource ? ['id' => $lead->leadSource->lead_sources_id, 'name' => $lead->leadSource->name] : null,
                'lead_stage'          => $lead->leadStage ? ['id' => $lead->leadStage->lead_stage_id, 'name' => $lead->leadStage->name] : null,
                'assigned_to'         => $lead->assignedUser ? ['id' => $lead->assignedUser->id, 'name' => $lead->assignedUser->name, 'email' => $lead->assignedUser->email] : null,
                'created_by'          => $lead->creator ? ['id' => $lead->creator->id, 'name' => $lead->creator->name] : null,
                'created_at'          => $lead->created_at ? $lead->created_at->format('Y-m-d H:i:s') : null,
                'closed_date'         => $lead->created_at ? $lead->created_at->format('d M Y') : null,
            ];
        });

        $responsePayload = [
            'status'  => true,
            'message' => 'Sale Closed report retrieved successfully.',
            'kpi'     => [
                'total_closed_count'     => $totalCount,
                'total_closed_amount'    => $totalAmount,
                'total_closed_amount_fmt' => '₹' . number_format($totalAmount, 2),
                'today_closed_count'     => $todayCount,
                'today_closed_amount'    => $todayAmount,
                'today_closed_amount_fmt' => '₹' . number_format($todayAmount, 2),
                'month_closed_count'     => $monthCount,
                'month_closed_amount'    => $monthAmount,
                'month_closed_amount_fmt' => '₹' . number_format($monthAmount, 2),
                'year_closed_count'      => $yearCount,
                'year_closed_amount'     => $yearAmount,
                'year_closed_amount_fmt' => '₹' . number_format($yearAmount, 2),
                'filtered_count'         => $filteredCount,
                'filtered_amount'        => $filteredAmount,
                'filtered_amount_fmt'    => '₹' . number_format($filteredAmount, 2),
            ],
            'active_filter' => [
                'filter_type' => $filterType,
                'date'        => $date,
                'month'       => $month,
                'year'        => $year,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
            ],
            'data' => $formattedLeads,
        ];

        if ($pagination) {
            $responsePayload['pagination'] = $pagination;
        }

        return response()->json($responsePayload);
    }

    /**
     * Get filter options for Sale Closed report (Branches, Teams, Staff, Requirements, Sources, Years).
     * Accessible via GET api/v1/sale-closed/filters or GET api/v1/reports/sale-closed/filters
     */
    public function getFilterOptions(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($user, 'sale-closed.view') && !$this->hasPermission($user, 'leads.view') && !$user->isStaff()) {
            return $this->permissionDeniedResponse('Sale Closed report filters');
        }

        // Branch options scoped by user role
        if ($user->isAdmin()) {
            $branches = Branch::where('status', 1)->orderBy('name')->get();
            $teams    = Team::where('status', 1)->orderBy('name')->get();
            $staffs   = User::staffOnly()->where('status', 1)->orderBy('name')->get(['id', 'name', 'email']);
        } elseif ($user->isBranchManager()) {
            $branchIds = $user->accessibleBranchIds() ?? [];
            $branches  = Branch::whereIn('id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $teams     = Team::whereIn('branch_id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $staffs    = User::staffOnly()->whereIn('branch_id', $branchIds)->where('status', 1)->orderBy('name')->get(['id', 'name', 'email']);
        } elseif ($user->isTeamLeader()) {
            $teamIds   = $user->accessibleTeamIds() ?? [];
            $branchIds = $user->accessibleBranchIds() ?? [];
            $branches  = Branch::whereIn('id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $teams     = Team::whereIn('id', $teamIds)->where('status', 1)->orderBy('name')->get();
            $staffs    = User::staffOnly()->whereIn('team_id', $teamIds)->where('status', 1)->orderBy('name')->get(['id', 'name', 'email']);
        } else {
            $branches = $user->branch_id ? Branch::where('id', $user->branch_id)->get() : collect();
            $teams    = $user->team_id ? Team::where('id', $user->team_id)->get() : collect();
            $staffs   = collect([$user]);
        }

        $leadRequirements = LeadRequirement::where('status', 1)->orderBy('name')->get(['lead_requirements_id as id', 'name']);
        $leadSources      = LeadSource::where('status', 1)->orderBy('name')->get(['lead_sources_id as id', 'name']);

        $currentYear = (int)date('Y');
        $years = range($currentYear, max(2023, $currentYear - 4));

        return response()->json([
            'status' => true,
            'message' => 'Filter options retrieved successfully.',
            'data' => [
                'filter_types' => [
                    ['value' => 'all', 'label' => 'All Closed Sales'],
                    ['value' => 'daily', 'label' => 'Daily (Today)'],
                    ['value' => 'monthly', 'label' => 'Monthly (This Month)'],
                    ['value' => 'yearly', 'label' => 'Yearly (This Year)'],
                    ['value' => 'custom', 'label' => 'Custom Date Range'],
                ],
                'branches' => $branches->map(fn($b) => ['id' => $b->id, 'value' => $b->id, 'name' => $b->name, 'code' => $b->code, 'label' => "{$b->name} ({$b->code})"]),
                'teams'    => $teams->map(fn($t) => ['id' => $t->id, 'value' => $t->id, 'branch_id' => $t->branch_id, 'name' => $t->name, 'code' => $t->code, 'label' => "{$t->name} ({$t->code})"]),
                'staffs'   => $staffs->map(fn($s) => ['id' => $s->id, 'value' => $s->id, 'name' => $s->name, 'email' => $s->email, 'label' => $s->name]),
                'lead_requirements' => $leadRequirements,
                'lead_sources'      => $leadSources,
                'years'             => $years,
            ],
        ]);
    }
}
