<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Branch;
use App\Models\Team;
use App\Models\CustomerCustomField;
use App\Models\LeadSetting;
use App\Models\LeadRequirement;
use App\Models\LeadStage;
use App\Models\Lead;
use App\Models\CallLog;
use App\Models\Followup;
use App\Models\DemoProcess;
use App\Models\Proposal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        if ($user && $user->isSuperAdmin()) {
            $staffs = User::staffOnly()->orderBy('name')->get();
            $branches = Branch::where('status', 1)->orderBy('name')->get();
            $teams = Team::where('status', 1)->orderBy('name')->get();
        } elseif ($user->isBranchManager()) {
            $branchIds = $user->accessibleBranchIds() ?? [];
            $staffs = User::staffOnly()->whereIn('branch_id', $branchIds)->orderBy('name')->get();
            $branches = Branch::whereIn('id', $branchIds)->where('status', 1)->orderBy('name')->get();
            $teams = Team::whereIn('branch_id', $branchIds)->where('status', 1)->orderBy('name')->get();
        } else {
            $staffs = User::where('id', Auth::id())->get();
            $branches = !empty($user->branch_id) ? Branch::where('id', $user->branch_id)->get() : collect();
            $teamIds = $user->accessibleTeamIds() ?? [];
            $teams = Team::whereIn('id', $teamIds)->where('status', 1)->orderBy('name')->get();
        }

        $allUsers = User::orderBy('name')->get();
        $customFields = CustomerCustomField::where('status', 1)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $leadRequirements = LeadRequirement::where('status', 1)->orderBy('name')->get();
        $leadStages = LeadStage::where('status', 1)->orderBy('sort_order')->orderBy('name')->get();

        $standardFields = [
            'customer_type'    => 'Type',
            'branch'           => 'Branch',
            'team'             => 'Team',
            'name'             => 'Name',
            'company_name'     => 'Company Name',
            'mobile'           => 'Mobile',
            'email'            => 'Email',
            'alternate_mobile' => 'Alternate Mobile',
            'address'          => 'Address',
            'city'             => 'City',
            'state'            => 'State',
            'country'          => 'Country',
            'pincode'          => 'Pincode',
            'lead_source'      => 'Lead Source',
            'lead_stage'       => 'Lead Stages',
            'lead_requirement' => 'Lead Requirements',
            'lost_reason'      => 'Lost Reason',
            'created_at'       => 'Created At',
            'created_by'       => 'Created By',
            'status'           => 'Status',
        ];

        $allAvailableFieldsMap = [];
        foreach ($standardFields as $key => $label) {
            $allAvailableFieldsMap[$key] = [
                'key'   => $key,
                'label' => $label,
                'type'  => 'standard',
            ];
        }

        foreach ($customFields as $cf) {
            $allAvailableFieldsMap[$cf->field_name] = [
                'key'   => $cf->field_name,
                'label' => $cf->field_label,
                'type'  => 'custom',
                'field' => $cf,
            ];
        }

        $setting = LeadSetting::getSettings();
        $savedColumns = $setting->customer_list_columns;

        if (empty($savedColumns) || !is_array($savedColumns)) {
            $savedColumns = array_keys($allAvailableFieldsMap);
        } else {
            $savedColumns = array_values(array_filter($savedColumns, function ($key) use ($allAvailableFieldsMap) {
                return isset($allAvailableFieldsMap[$key]);
            }));
        }

        $visibleColumns = [];
        foreach ($savedColumns as $colKey) {
            if (isset($allAvailableFieldsMap[$colKey])) {
                $visibleColumns[] = $allAvailableFieldsMap[$colKey];
            }
        }

        return view('customers.view', compact('staffs', 'branches', 'teams', 'allUsers', 'customFields', 'visibleColumns', 'leadRequirements', 'leadStages'));
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();

        $query = Customer::forUser(Auth::user())->qualifiedForCustomerList();

        if ($request->filled('branch_id')) {
            $query->where('customers.branch_id', $request->input('branch_id'));
        }

        if ($request->filled('team_id')) {
            $query->where('customers.team_id', $request->input('team_id'));
        }

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        if ($request->filled('lead_requirement_id')) {
            $leadReqId = $request->input('lead_requirement_id');
            $query->where(function ($q) use ($leadReqId) {
                $q->where('lead_requirement_id', $leadReqId)
                  ->orWhereHas('leads', function ($lq) use ($leadReqId) {
                      $lq->where('lead_requirement_id', $leadReqId);
                  });
            });
        }

        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        $customers = (clone $query)->with([
            'branch:id,name,code',
            'team:id,name,code',
            'branches:id,name,code',
            'teams:id,name,code',
            'creator:id,name',
            'owner:id,name',
            'assignedBy:id,name',
            'leadRequirement:lead_requirements_id,name',
            'leadStage:lead_stage_id,name',
            'latestLead.leadSource:lead_sources_id,name',
            'latestLead.leadStage:lead_stage_id,name',
            'latestLead.leadRequirement:lead_requirements_id,name',
            'latestLead.lostReason:lost_reason_id,reason',
        ])
            ->orderBy('customer_id', 'DESC')
            ->get();

        $baseCountQuery = Customer::forUser(Auth::user())->qualifiedForCustomerList();

        if ($request->filled('branch_id')) {
            $baseCountQuery->where('customers.branch_id', $request->input('branch_id'));
        }

        if ($request->filled('team_id')) {
            $baseCountQuery->where('customers.team_id', $request->input('team_id'));
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $baseCountQuery->where('status', $request->input('status'));
        }

        if ($request->filled('created_by')) {
            $baseCountQuery->where('created_by', $request->input('created_by'));
        }

        if ($request->filled('lead_requirement_id')) {
            $leadReqId = $request->input('lead_requirement_id');
            $baseCountQuery->where(function ($q) use ($leadReqId) {
                $q->where('lead_requirement_id', $leadReqId)
                  ->orWhereHas('leads', function ($lq) use ($leadReqId) {
                      $lq->where('lead_requirement_id', $leadReqId);
                  });
            });
        }

        if ($filterType === 'daily' && !empty($date)) {
            $baseCountQuery->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $baseCountQuery->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $baseCountQuery->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $baseCountQuery->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $baseCountQuery->whereDate('created_at', '<=', $endDate);
            }
        }

        $resellcount = (clone $baseCountQuery)->where('customer_type', 'reseller')->count();
        $user        = (clone $baseCountQuery)->where('customer_type', 'user')->count();
        $staffcount  = (clone $baseCountQuery)->whereNotNull('created_by')->count();

        return response()->json([
            'status'      => true,
            'data'        => $customers,
            'resellcount' => $resellcount,
            'user'        => $user,
            'staffcount'  => $staffcount
        ]);
    }

    public function store(Request $request)
    {
        // If an existing soft-deleted customer holds this mobile number, free it first
        if ($request->filled('mobile')) {
            $trashed = Customer::onlyTrashed()->where('mobile', trim((string)$request->input('mobile')))->first();
            if ($trashed) {
                $suffix = '_d' . $trashed->customer_id;
                $maxLen = 20 - strlen($suffix);
                $trashed->mobile = substr($trashed->mobile, 0, $maxLen) . $suffix;
                $trashed->saveQuietly();
            }
        }

        [$customRules, $customAttributes] = $this->getCustomFieldsRules();

        $baseRules = [
            'customer_type'    => ['required', 'in:user,reseller'],
            'branch_ids'       => ['nullable', 'array'],
            'branch_ids.*'     => ['integer', 'exists:branches,id'],
            'team_ids'         => ['nullable', 'array'],
            'team_ids.*'       => ['integer', 'exists:teams,id'],
            'branch_id'        => ['nullable', 'exists:branches,id'],
            'team_id'          => ['nullable', 'exists:teams,id'],
            'name'             => ['required', 'string', 'max:255'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'mobile'           => ['required', 'string', 'max:10', Rule::unique('customers', 'mobile')->withoutTrashed()],
            'email'            => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:10'],
            'address'          => ['nullable', 'string'],
            'city'             => ['nullable', 'string', 'max:100'],
            'state'            => ['nullable', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'max:100'],
            'pincode'          => ['nullable', 'string', 'max:10'],
            'owner_by'            => ['nullable', 'exists:users,id'],
            'assign_by'           => ['nullable', 'exists:users,id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'status'              => ['required', 'in:0,1'],
        ];

        $rules = array_merge($baseRules, $customRules);
        $validated = $request->validate($rules, [], $customAttributes);

        $currentUser = Auth::user();
        $validated['created_by'] = $currentUser->id;

        // Process multiple branches
        $branchIds = (array) $request->input('branch_ids', []);
        if (empty($branchIds) && !empty($validated['branch_id'])) {
            $branchIds = [$validated['branch_id']];
        }
        $validated['branch_id'] = !empty($branchIds) ? $branchIds[0] : $currentUser->branch_id;

        // Process multiple teams
        $teamIds = (array) $request->input('team_ids', []);
        if (empty($teamIds) && !empty($validated['team_id'])) {
            $teamIds = [$validated['team_id']];
        }
        $validated['team_id'] = !empty($teamIds) ? $teamIds[0] : $currentUser->team_id;

        unset($validated['branch_ids'], $validated['team_ids']);
        $validated['custom_fields'] = $this->processCustomFieldsPayload($validated['custom_fields'] ?? []);

        try {
            $customer = Customer::create($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'The mobile number has already been taken.',
                    'errors'  => ['mobile' => ['The mobile number has already been taken.']],
                ], 422);
            }
            throw $e;
        }

        // Sync multiple branches
        if (!empty($branchIds)) {
            $branchSync = [];
            foreach ($branchIds as $idx => $bId) {
                $branchSync[$bId] = ['is_primary' => ($idx === 0)];
            }
            $customer->branches()->sync($branchSync);
        } else {
            $customer->branches()->detach();
        }

        // Sync multiple teams
        if (!empty($teamIds)) {
            $teamSync = [];
            foreach ($teamIds as $idx => $tId) {
                $teamSync[$tId] = ['is_primary' => ($idx === 0)];
            }
            $customer->teams()->sync($teamSync);
        } else {
            $customer->teams()->detach();
        }

        if (!empty($validated['custom_fields']['followup_date'])) {
            $this->syncFollowupDate($customer, $validated['custom_fields']['followup_date']);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->loadMissing([
            'branch:id,name,code',
            'team:id,name,code',
            'branches:id,name,code',
            'teams:id,name,code',
            'leadRequirement:lead_requirements_id,name',
            'leadStage:lead_stage_id,name',
        ]);

        $assignedBranchIds = $customer->branches->pluck('id')->values()->all();
        if (empty($assignedBranchIds) && !empty($customer->branch_id)) {
            $assignedBranchIds = [$customer->branch_id];
        }

        $assignedTeamIds = $customer->teams->pluck('id')->values()->all();
        if (empty($assignedTeamIds) && !empty($customer->team_id)) {
            $assignedTeamIds = [$customer->team_id];
        }

        $customerData = $customer->toArray();
        $customerData['branch_ids'] = $assignedBranchIds;
        $customerData['team_ids']   = $assignedTeamIds;

        return response()->json([
            'status' => true,
            'data'   => $customerData
        ]);
    }

    /**
     * Get full details of a customer including staff call history and sale closed status.
     * GET /admin/customers/details/{id}
     */
    public function getDetails($id)
    {
        $user = request()->user() ?? Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$user->isAdmin() && !$user->can('customers.view') && !$user->can('leads.view')) {
            return response()->json(['status' => false, 'message' => 'Access denied.'], 403);
        }

        $customer = Customer::with([
            'branch:id,name,code',
            'team:id,name,code',
            'branches:id,name,code',
            'teams:id,name,code',
            'creator:id,name,email',
            'owner:id,name,email',
            'assignedBy:id,name,email',
            'leadRequirement:lead_requirements_id,name',
            'leadStage:lead_stage_id,name',
            'leads' => function ($q) {
                $q->with([
                    'leadStage:lead_stage_id,name',
                    'leadRequirement:lead_requirements_id,name',
                    'leadSource:lead_sources_id,name',
                    'assignedUser:id,name,email',
                    'creator:id,name,email',
                    'branch:id,name,code',
                    'team:id,name,code',
                ])->orderBy('lead_id', 'desc');
            },
            'followups' => function ($q) {
                $q->with([
                    'forwardToUser:id,name,email',
                    'creator:id,name,email',
                    'lead:lead_id,lead_title',
                ])->orderBy('followups_id', 'desc');
            },
        ])->find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Customer not found.'], 404);
        }

        // 1. Resolve all Call Logs for this customer (direct customer_id, customer mobile, or customer lead IDs)
        $leadIds = $customer->leads->pluck('lead_id')->toArray();
        $callLogs = CallLog::where(function ($q) use ($customer, $leadIds) {
            $q->where('customer_id', $customer->customer_id);
            if (!empty($customer->mobile)) {
                $q->orWhere('phone', $customer->mobile);
            }
            if (!empty($leadIds)) {
                $q->orWhereIn('lead_id', $leadIds);
            }
        })
        ->with([
            'user:id,name,email',
            'recording:call_id,recording_file,duration',
            'lead:lead_id,lead_title',
        ])
        ->orderBy('call_start_time', 'desc')
        ->orderBy('call_id', 'desc')
        ->get();

        $formattedCalls = $callLogs->map(function ($cl) {
            $time = $cl->call_start_time ?? $cl->created_at;
            return [
                'call_id'          => $cl->call_id,
                'staff_name'       => $cl->user?->name ?? 'Staff',
                'staff_email'      => $cl->user?->email ?? '',
                'phone'            => $cl->phone ?? '',
                'call_type'        => ucfirst($cl->call_type ?? 'Out'),
                'call_status'      => ucfirst($cl->call_status ?? 'Answered'),
                'duration'         => $cl->duration ?: '00:00',
                'lead_title'       => $cl->lead?->lead_title,
                'call_date'        => $time ? Carbon::parse($time)->format('d-m-Y h:i A') : '-',
                'time_ago'         => $time ? Carbon::parse($time)->diffForHumans() : '-',
                'recording_url'    => $cl->recording_url,
            ];
        });

        // 2. Resolve Sale Closed details
        $saleClosedStage = LeadStage::where(function ($q) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%sale%close%'])
              ->orWhere('lead_stage_id', 6);
        })->first();
        $saleClosedStageId = $saleClosedStage ? $saleClosedStage->lead_stage_id : 6;

        $saleClosedLead = $customer->leads->first(function ($l) use ($saleClosedStageId) {
            return $l->lead_stage_id == $saleClosedStageId ||
                ($l->leadStage && str_contains(strtolower($l->leadStage->name), 'close'));
        });

        $isSaleClosed = !empty($saleClosedLead) ||
            ($customer->lead_stage_id == $saleClosedStageId) ||
            ($customer->leadStage && str_contains(strtolower($customer->leadStage->name), 'close'));

        $saleClosedDetails = null;
        if ($isSaleClosed) {
            $closedTime = $saleClosedLead?->updated_at ?? $saleClosedLead?->created_at ?? $customer->updated_at;
            $closedStaff = $saleClosedLead?->assignedUser?->name ?? $saleClosedLead?->creator?->name ?? $customer->owner?->name ?? $customer->creator?->name ?? 'Staff';
            $saleClosedDetails = [
                'is_closed'       => true,
                'lead_id'         => $saleClosedLead?->lead_id,
                'lead_title'      => $saleClosedLead?->lead_title ?? 'Direct Sale',
                'closed_by'       => $closedStaff,
                'closed_date'     => $closedTime ? Carbon::parse($closedTime)->format('d-m-Y h:i A') : '-',
                'closed_date_raw' => $closedTime ? Carbon::parse($closedTime)->toIso8601String() : null,
                'closed_ago'      => $closedTime ? Carbon::parse($closedTime)->diffForHumans() : '-',
                'requirement'     => $saleClosedLead?->leadRequirement?->name ?? $customer->leadRequirement?->name ?? 'N/A',
                'expected_amount' => $saleClosedLead?->expected_amount ? '₹' . number_format($saleClosedLead->expected_amount, 2) : null,
            ];
        }

        // 3. Formatted Leads
        $formattedLeads = $customer->leads->map(function ($l) use ($saleClosedStageId) {
            $isClosed = $l->lead_stage_id == $saleClosedStageId ||
                ($l->leadStage && str_contains(strtolower($l->leadStage->name), 'close'));
            return [
                'lead_id'          => $l->lead_id,
                'lead_title'       => $l->lead_title,
                'stage_name'       => $l->leadStage?->name ?? 'New',
                'is_sale_closed'   => $isClosed,
                'requirement_name' => $l->leadRequirement?->name ?? '-',
                'source_name'      => $l->leadSource?->name ?? '-',
                'assigned_to'      => $l->assignedUser?->name ?? 'Unassigned',
                'created_at'       => $l->created_at ? Carbon::parse($l->created_at)->format('d-m-Y') : '-',
                'updated_at'       => $l->updated_at ? Carbon::parse($l->updated_at)->format('d-m-Y h:i A') : '-',
            ];
        });

        // 4. Formatted Followups
        $formattedFollowups = $customer->followups->map(function ($f) {
            return [
                'followup_id'        => $f->followups_id,
                'lead_title'         => $f->lead?->lead_title ?? 'General',
                'staff_name'         => $f->forwardToUser?->name ?? $f->creator?->name ?? 'Staff',
                'next_followup_date' => $f->next_followup_date ? Carbon::parse($f->next_followup_date)->format('d-m-Y') : '-',
                'remarks'            => $f->remarks ?? '-',
                'status'             => $f->followup_status ?? 'Pending',
                'created_at'         => $f->created_at ? Carbon::parse($f->created_at)->format('d-m-Y') : '-',
            ];
        });

        // 5. Formatted Demo Processes for this customer (matched by customer phone or name)
        $demoProcesses = DemoProcess::where(function ($q) use ($customer) {
            if (!empty($customer->mobile)) {
                $q->where('customer_phone', $customer->mobile);
            }
            if (!empty($customer->name)) {
                $q->orWhere('customer_name', $customer->name);
            }
        })
        ->with([
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'creator:id,name',
            'assignedUser:id,name',
        ])
        ->orderBy('demo_date', 'desc')
        ->orderBy('demo_time', 'desc')
        ->orderBy('demo_process_id', 'desc')
        ->get();

        $formattedDemos = $demoProcesses->map(function ($dp) {
            $dateStr = $dp->demo_date ? Carbon::parse($dp->demo_date)->format('d-m-Y') : '-';
            $timeStr = $dp->demo_time ? trim($dp->demo_time) : '';
            $dateTime = $timeStr ? "{$dateStr} {$timeStr}" : $dateStr;
            return [
                'demo_process_id'  => $dp->demo_process_id,
                'customer_name'    => $dp->customer_name ?? '-',
                'customer_phone'   => $dp->customer_phone ?? '-',
                'requirement_name' => $dp->leadRequirement?->name ?? '-',
                'source_name'      => $dp->leadSource?->name ?? '-',
                'assigned_staff'   => $dp->assignedUser?->name ?? $dp->creator?->name ?? 'Staff',
                'demo_date'        => $dateStr,
                'demo_time'        => $timeStr ?: '-',
                'demo_datetime'    => $dateTime,
                'status'           => ucfirst($dp->status ?? 'Pending'),
                'remarks'          => $dp->remarks ?? '-',
                'created_at'       => $dp->created_at ? Carbon::parse($dp->created_at)->format('d-m-Y') : '-',
            ];
        });

        // 6. Formatted Proposals for this customer (matched by customer_id, mobile, or name)
        $proposals = Proposal::where(function ($q) use ($customer) {
            $q->where('customer_id', $customer->customer_id);
            if (!empty($customer->mobile)) {
                $q->orWhere('customer_mobile', $customer->mobile);
            }
            if (!empty($customer->name)) {
                $q->orWhere('customer_name', $customer->name);
            }
        })
        ->with([
            'leadRequirement:lead_requirements_id,name',
            'salesManager:id,name',
            'creator:id,name',
        ])
        ->withCount('items')
        ->orderBy('proposal_id', 'desc')
        ->get();

        $formattedProposals = $proposals->map(function ($pr) {
            return [
                'proposal_id'      => $pr->proposal_id,
                'proposal_number'  => $pr->proposal_number ?: ('#' . $pr->proposal_id),
                'proposal_date'    => $pr->created_at ? Carbon::parse($pr->created_at)->format('d-m-Y') : '-',
                'requirement_name' => $pr->leadRequirement?->name ?? $pr->lead_requirement_name ?? '-',
                'sales_manager'    => $pr->salesManager?->name ?? $pr->sales_manager_name ?? $pr->creator?->name ?? 'Staff',
                'items_count'      => $pr->items_count ?? 0,
                'subtotal'         => '₹' . number_format((float)($pr->subtotal ?? 0), 2),
                'total_tax'        => '₹' . number_format((float)($pr->total_tax ?? 0), 2),
                'total_amount'     => '₹' . number_format((float)($pr->total_amount ?? 0), 2),
                'status'           => ucfirst($pr->status ?? 'Draft'),
                'created_at'       => $pr->created_at ? Carbon::parse($pr->created_at)->format('d-m-Y') : '-',
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => [
                'customer' => [
                    'customer_id'      => $customer->customer_id,
                    'name'             => $customer->name,
                    'customer_type'    => ucfirst($customer->customer_type ?? 'User'),
                    'mobile'           => $customer->mobile,
                    'email'            => $customer->email ?: '-',
                    'company_name'     => $customer->company_name ?: '-',
                    'city'             => $customer->city ?: '-',
                    'address'          => $customer->address ?: '-',
                    'branch_name'      => $customer->branch?->name ?: ($customer->branches->pluck('name')->implode(', ') ?: '-'),
                    'team_name'        => $customer->team?->name ?: ($customer->teams->pluck('name')->implode(', ') ?: '-'),
                    'branches'         => $customer->branches->pluck('name'),
                    'teams'            => $customer->teams->pluck('name'),
                    'owner_name'       => $customer->owner?->name ?: '-',
                    'creator_name'     => $customer->creator?->name ?: '-',
                    'assigned_by_name' => $customer->assignedBy?->name ?: '-',
                    'created_at'       => $customer->created_at ? Carbon::parse($customer->created_at)->format('d-m-Y h:i A') : '-',
                ],
                'sale_closed'    => $saleClosedDetails,
                'call_logs'      => $formattedCalls,
                'leads'          => $formattedLeads,
                'demo_processes' => $formattedDemos,
                'proposals'      => $formattedProposals,
                'followups'      => $formattedFollowups,
                'stats'          => [
                    'total_calls'          => $formattedCalls->count(),
                    'total_leads'          => $formattedLeads->count(),
                    'is_sale_closed'       => $isSaleClosed,
                    'total_demo_processes' => $formattedDemos->count(),
                    'total_proposals'      => $formattedProposals->count(),
                    'total_followups'      => $formattedFollowups->count(),
                ],
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        // If an existing soft-deleted customer holds this mobile number, free it first
        if ($request->filled('mobile')) {
            $trashed = Customer::onlyTrashed()
                ->where('mobile', trim((string)$request->input('mobile')))
                ->where('customer_id', '!=', $customer->customer_id)
                ->first();
            if ($trashed) {
                $suffix = '_d' . $trashed->customer_id;
                $maxLen = 20 - strlen($suffix);
                $trashed->mobile = substr($trashed->mobile, 0, $maxLen) . $suffix;
                $trashed->saveQuietly();
            }
        }

        [$customRules, $customAttributes] = $this->getCustomFieldsRules();

        $baseRules = [
            'customer_type'       => ['required', 'in:user,reseller'],
            'branch_ids'          => ['nullable', 'array'],
            'branch_ids.*'        => ['integer', 'exists:branches,id'],
            'team_ids'            => ['nullable', 'array'],
            'team_ids.*'          => ['integer', 'exists:teams,id'],
            'branch_id'           => ['nullable', 'exists:branches,id'],
            'team_id'             => ['nullable', 'exists:teams,id'],
            'name'                => ['required', 'string', 'max:255'],
            'company_name'        => ['nullable', 'string', 'max:255'],
            'mobile'              => ['required', 'string', 'max:10', Rule::unique('customers', 'mobile')->ignore($customer->customer_id, 'customer_id')->withoutTrashed()],
            'email'               => ['nullable', 'email', 'max:255'],
            'alternate_mobile'    => ['nullable', 'string', 'max:10'],
            'address'             => ['nullable', 'string'],
            'city'                => ['nullable', 'string', 'max:100'],
            'state'               => ['nullable', 'string', 'max:100'],
            'country'             => ['nullable', 'string', 'max:100'],
            'pincode'             => ['nullable', 'string', 'max:10'],
            'owner_by'            => ['nullable', 'exists:users,id'],
            'assign_by'           => ['nullable', 'exists:users,id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'status'              => ['required', 'in:0,1'],
        ];

        $rules = array_merge($baseRules, $customRules);
        $validated = $request->validate($rules, [], $customAttributes);

        // Process multiple branches
        $branchIds = (array) $request->input('branch_ids', []);
        if (empty($branchIds) && !empty($validated['branch_id'])) {
            $branchIds = [$validated['branch_id']];
        }
        $validated['branch_id'] = !empty($branchIds) ? $branchIds[0] : null;

        // Process multiple teams
        $teamIds = (array) $request->input('team_ids', []);
        if (empty($teamIds) && !empty($validated['team_id'])) {
            $teamIds = [$validated['team_id']];
        }
        $validated['team_id'] = !empty($teamIds) ? $teamIds[0] : null;

        unset($validated['branch_ids'], $validated['team_ids']);
        $validated['custom_fields'] = $this->processCustomFieldsPayload($validated['custom_fields'] ?? []);

        try {
            $customer->update($validated);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062 || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'status'  => false,
                    'message' => 'The mobile number has already been taken.',
                    'errors'  => ['mobile' => ['The mobile number has already been taken.']],
                ], 422);
            }
            throw $e;
        }

        // Sync multiple branches
        if (!empty($branchIds)) {
            $branchSync = [];
            foreach ($branchIds as $idx => $bId) {
                $branchSync[$bId] = ['is_primary' => ($idx === 0)];
            }
            $customer->branches()->sync($branchSync);
        } else {
            $customer->branches()->detach();
        }

        // Sync multiple teams
        if (!empty($teamIds)) {
            $teamSync = [];
            foreach ($teamIds as $idx => $tId) {
                $teamSync[$tId] = ['is_primary' => ($idx === 0)];
            }
            $customer->teams()->sync($teamSync);
        } else {
            $customer->teams()->detach();
        }

        if (isset($validated['custom_fields']['followup_date'])) {
            $this->syncFollowupDate($customer, $validated['custom_fields']['followup_date']);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Customer updated successfully.',
            'data'    => $customer
        ]);
    }

    /**
     * Transfer customer to another Branch, Team, and/or Owner
     */
    public function transfer(Request $request, $id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $validated = $request->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'team_id'   => ['required', 'exists:teams,id'],
            'owner_by'  => ['nullable', 'exists:users,id'],
        ]);

        $customer->update($validated);

        // Also cascade branch & team to associated leads
        $customer->leads()->update([
            'branch_id' => $validated['branch_id'],
            'team_id'   => $validated['team_id'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Customer and associated leads transferred successfully.'
        ]);
    }

    /**
     * Sync Customer "Followup Date" custom field with Follow-ups module
     */
    protected function syncFollowupDate(Customer $customer, ?string $followupDate): void
    {
        if (empty($followupDate)) {
            return;
        }

        try {
            $parsedDate = Carbon::parse($followupDate)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return;
        }

        $followup = Followup::where('customer_id', $customer->customer_id)
            ->where('followup_status', 'Pending')
            ->latest('followups_id')
            ->first();

        if ($followup) {
            $followup->update([
                'next_followup_date' => $parsedDate,
                'forward_to'         => $customer->assign_by ?? $customer->owner_by ?? $followup->forward_to,
            ]);
        } else {
            Followup::create([
                'customer_id'        => $customer->customer_id,
                'lead_id'            => $customer->latestLead?->lead_id,
                'followup_type'      => 'Call',
                'duration'           => '5 minutes',
                'remarks'            => 'Follow-up for customer ' . $customer->name,
                'next_followup_date' => $parsedDate,
                'followup_status'    => 'Pending',
                'forward_to'         => $customer->assign_by ?? $customer->owner_by,
                'created_by'         => Auth::id() ?? $customer->created_by,
            ]);
        }

        // If customer has an associated lead, also sync lead's next_followup_date
        if ($customer->latestLead) {
            $customer->latestLead->update([
                'next_followup_date' => Carbon::parse($followupDate)->format('Y-m-d')
            ]);
        }
    }

    public function destroy($id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $customer = Customer::forUser(Auth::user())->find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $customer->status = $customer->status == 1 ? 0 : 1;
        $customer->save();

        return response()->json([
            'status'  => true,
            'message' => 'Customer status updated successfully.',
            'new_status' => $customer->status
        ]);
    }
    /**
     * Export Customers to Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $query = Customer::forUser(Auth::user())->qualifiedForCustomerList();

        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->input('customer_type'));
        }

        if ($request->filled('status') && $request->input('status') !== '') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('created_at', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                ->whereMonth('created_at', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        $customers = $query->with(['creator:id,name', 'owner:id,name', 'assignedBy:id,name'])
            ->orderBy('customer_id', 'DESC')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customers');

        $headers = [
            'S.No', 'Customer Type', 'Name', 'Company Name', 'Mobile',
            'Email', 'Alternate Mobile', 'Address', 'City', 'State',
            'Country', 'Pincode', 'Status', 'Created By', 'Created At'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($customers as $index => $c) {
            $sheet->fromArray([
                $index + 1,
                ucfirst($c->customer_type),
                $c->name,
                $c->company_name ?? '-',
                $c->mobile,
                $c->email ?? '-',
                $c->alternate_mobile ?? '-',
                $c->address ?? '-',
                $c->city ?? '-',
                $c->state ?? '-',
                $c->country ?? '-',
                $c->pincode ?? '-',
                $c->status == 1 ? 'Active' : 'Inactive',
                $c->creator ? $c->creator->name : '-',
                $c->created_at ? $c->created_at->format('d-m-Y H:i') : '-'
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        $fileName = 'customers_export_' . date('Y_m_d_H_i_s') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Download sample Excel template for bulk customer import.
     */
    public function downloadSampleExcel()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Sample Customers');

        $headers = [
            'Customer Type', 'Name', 'Company Name', 'Mobile', 'Email',
            'Alternate Mobile', 'Address', 'City', 'State', 'Country', 'Pincode', 'Lead Stage',
            'Branch Code', 'Team Code', 'Staff Email'
        ];

        $sheet->fromArray($headers, null, 'A1');

        $sampleData = [
            ['user', 'John Doe', 'ABC Enterprises', '9876543210', 'john@example.com', '9876543211', '123 Main St', 'Chennai', 'Tamil Nadu', 'India', '600001', 'Sale closed', 'BR-MAIN', 'TM-MAIN', 'admin@gmail.com'],
            ['reseller', 'Jane Smith', 'XYZ Solutions', '9123456780', 'jane@example.com', '', '456 Cross Rd', 'Madurai', 'Tamil Nadu', 'India', '625001', 'Sale closed', 'BR-MAIN', 'TM-MAIN', ''],
        ];

        $sheet->fromArray($sampleData, null, 'A2');

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'customer_sample_import.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import customers from Excel or CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file'   => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'branch_id'    => 'nullable',
            'branch_ids'   => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'team_id'      => 'nullable',
            'team_ids'     => 'nullable|array',
            'team_ids.*'   => 'exists:teams,id',
            'owner_by'     => 'nullable|exists:users,id',
        ]);

        $file = $request->file('excel_file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, false);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Unable to read the uploaded file: ' . $e->getMessage()
            ], 422);
        }

        if (empty($data) || count($data) < 2) {
            return response()->json([
                'status'  => false,
                'message' => 'The uploaded file is empty or missing data rows.'
            ], 422);
        }

        // Defaults from request or logged in user
        $auth = Auth::user();

        // Resolve branch IDs (array + single fallback)
        $defaultBranchIds = $request->input('branch_ids', []);
        if (empty($defaultBranchIds) && $request->filled('branch_id')) {
            $defaultBranchIds = [$request->input('branch_id')];
        }
        $defaultBranchIds = array_values(array_filter(array_unique(array_map('intval', (array)$defaultBranchIds))));

        if (empty($defaultBranchIds) && $auth?->branch_id) {
            $defaultBranchIds = [$auth->branch_id];
        }
        if (empty($defaultBranchIds)) {
            $mainB = Branch::where('code', 'BR-MAIN')->value('id');
            if ($mainB) {
                $defaultBranchIds = [$mainB];
            }
        }
        $defaultBranchId = $defaultBranchIds[0] ?? null;

        // Resolve team IDs (array + single fallback)
        $defaultTeamIds = $request->input('team_ids', []);
        if (empty($defaultTeamIds) && $request->filled('team_id')) {
            $defaultTeamIds = [$request->input('team_id')];
        }
        $defaultTeamIds = array_values(array_filter(array_unique(array_map('intval', (array)$defaultTeamIds))));

        if (empty($defaultTeamIds) && $auth?->team_id) {
            $defaultTeamIds = [$auth->team_id];
        }
        if (empty($defaultTeamIds)) {
            $mainT = Team::where('code', 'TM-MAIN')->value('id');
            if ($mainT) {
                $defaultTeamIds = [$mainT];
            }
        }
        $defaultTeamId = $defaultTeamIds[0] ?? null;

        $defaultOwnerBy  = $request->input('owner_by')  ?: null;

        // Normalize header row
        $headerRow = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$h)));
        }, $data[0] ?? []);

        $columnMap = [
            'customertype'    => 'customer_type',
            'type'            => 'customer_type',
            'name'            => 'name',
            'customername'    => 'name',
            'companyname'     => 'company_name',
            'company'         => 'company_name',
            'mobile'          => 'mobile',
            'mobilenumber'    => 'mobile',
            'phone'           => 'mobile',
            'phonenumber'     => 'mobile',
            'email'           => 'email',
            'emailaddress'    => 'email',
            'alternatemobile' => 'alternate_mobile',
            'altmobile'       => 'alternate_mobile',
            'address'         => 'address',
            'city'            => 'city',
            'state'           => 'state',
            'country'         => 'country',
            'pincode'         => 'pincode',
            'postalcode'      => 'pincode',
            'zipcode'         => 'pincode',
            'leadstage'       => 'lead_stage',
            'leadstageid'     => 'lead_stage',
            'leadstagename'   => 'lead_stage',
            'stage'           => 'lead_stage',
            'stageid'         => 'lead_stage',
            'stagename'       => 'lead_stage',
            'branch'          => 'branch',
            'branchcode'      => 'branch',
            'branchname'      => 'branch',
            'branchid'        => 'branch',
            'team'            => 'team',
            'teamcode'        => 'team',
            'teamname'        => 'team',
            'teamid'          => 'team',
            'owner'           => 'owner_by',
            'ownerby'         => 'owner_by',
            'staff'           => 'owner_by',
            'staffemail'      => 'owner_by',
            'staffname'       => 'owner_by',
            'staffid'         => 'owner_by',
            'assignedto'      => 'owner_by',
        ];

        $headerIndexes = [];
        foreach ($headerRow as $idx => $normHeader) {
            if (isset($columnMap[$normHeader])) {
                $headerIndexes[$columnMap[$normHeader]] = $idx;
            }
        }

        if (!isset($headerIndexes['mobile']) || !isset($headerIndexes['name'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Required columns "Name" and "Mobile" were not found in the file headers.'
            ], 422);
        }

        $leadStages = LeadStage::all();
        $leadStageMap = [];
        foreach ($leadStages as $ls) {
            $leadStageMap[(string)$ls->lead_stage_id] = $ls->lead_stage_id;
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$ls->name)));
            $leadStageMap[$cleanName] = $ls->lead_stage_id;
        }

        // Branch lookup map (by id, code, and clean name)
        $branches = Branch::all();
        $branchMap = [];
        foreach ($branches as $b) {
            $branchMap[(string)$b->id] = $b->id;
            $cleanCode = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$b->code)));
            $branchMap[$cleanCode] = $b->id;
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$b->name)));
            $branchMap[$cleanName] = $b->id;
        }

        // Team lookup map (by id, code, and clean name)
        $teams = Team::all();
        $teamMap = [];
        foreach ($teams as $t) {
            $teamMap[(string)$t->id] = $t->id;
            $cleanCode = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$t->code)));
            $teamMap[$cleanCode] = $t->id;
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$t->name)));
            $teamMap[$cleanName] = $t->id;
        }

        // Staff / User lookup map (by id, email, and clean name)
        $allStaff = User::all();
        $staffMap = [];
        foreach ($allStaff as $st) {
            $staffMap[(string)$st->id] = $st->id;
            if ($st->email) {
                $staffMap[strtolower(trim($st->email))] = $st->id;
            }
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$st->name)));
            $staffMap[$cleanName] = $st->id;
        }

        $saleClosedStage = $leadStages->first(function ($s) {
            $low = strtolower($s->name);
            return str_contains($low, 'sale') && str_contains($low, 'close');
        });
        $defaultSaleClosedId = $saleClosedStage ? $saleClosedStage->lead_stage_id : null;

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $userId = Auth::id();

        for ($i = 1; $i < count($data); $i++) {
            $row = $data[$i];

            // If row is entirely empty, skip
            if (empty(array_filter($row, fn($val) => $val !== null && trim((string)$val) !== ''))) {
                continue;
            }

            $name = isset($headerIndexes['name']) ? trim((string)($row[$headerIndexes['name']] ?? '')) : '';
            $mobile = isset($headerIndexes['mobile']) ? trim((string)($row[$headerIndexes['mobile']] ?? '')) : '';
            $mobileClean = preg_replace('/[^0-9]/', '', $mobile);

            if (strlen($mobileClean) > 10 && str_starts_with($mobileClean, '91')) {
                $mobileClean = substr($mobileClean, 2);
            }

            if (empty($name) || empty($mobileClean)) {
                $skipped++;
                continue;
            }

            $customerTypeRaw = isset($headerIndexes['customer_type']) ? strtolower(trim((string)($row[$headerIndexes['customer_type']] ?? ''))) : 'user';
            $customerType = in_array($customerTypeRaw, ['reseller'], true) ? 'reseller' : 'user';

            $email = isset($headerIndexes['email']) ? trim((string)($row[$headerIndexes['email']] ?? '')) : null;
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = null;
            }

            $altMobile = isset($headerIndexes['alternate_mobile']) ? preg_replace('/[^0-9]/', '', (string)($row[$headerIndexes['alternate_mobile']] ?? '')) : null;
            if (strlen((string)$altMobile) > 10 && str_starts_with((string)$altMobile, '91')) {
                $altMobile = substr((string)$altMobile, 2);
            }
            if (empty($altMobile)) {
                $altMobile = null;
            }

            $companyName = isset($headerIndexes['company_name']) ? trim((string)($row[$headerIndexes['company_name']] ?? '')) : null;
            $address = isset($headerIndexes['address']) ? trim((string)($row[$headerIndexes['address']] ?? '')) : null;
            $city = isset($headerIndexes['city']) ? trim((string)($row[$headerIndexes['city']] ?? '')) : null;
            $state = isset($headerIndexes['state']) ? trim((string)($row[$headerIndexes['state']] ?? '')) : null;
            $country = isset($headerIndexes['country']) ? trim((string)($row[$headerIndexes['country']] ?? '')) : 'India';
            $pincode = isset($headerIndexes['pincode']) ? trim((string)($row[$headerIndexes['pincode']] ?? '')) : null;

            // Resolve Branch
            $rowBranchId = $defaultBranchId;
            $rowBranchIds = $defaultBranchIds;
            if (isset($headerIndexes['branch'])) {
                $rawBranch = trim((string)($row[$headerIndexes['branch']] ?? ''));
                if ($rawBranch !== '') {
                    $cleanB = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rawBranch)));
                    if (isset($branchMap[$cleanB])) {
                        $rowBranchId = $branchMap[$cleanB];
                        $rowBranchIds = [$rowBranchId];
                    } elseif (isset($branchMap[$rawBranch])) {
                        $rowBranchId = $branchMap[$rawBranch];
                        $rowBranchIds = [$rowBranchId];
                    }
                }
            }

            // Resolve Team
            $rowTeamId = $defaultTeamId;
            $rowTeamIds = $defaultTeamIds;
            if (isset($headerIndexes['team'])) {
                $rawTeam = trim((string)($row[$headerIndexes['team']] ?? ''));
                if ($rawTeam !== '') {
                    $cleanT = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rawTeam)));
                    if (isset($teamMap[$cleanT])) {
                        $rowTeamId = $teamMap[$cleanT];
                        $rowTeamIds = [$rowTeamId];
                    } elseif (isset($teamMap[$rawTeam])) {
                        $rowTeamId = $teamMap[$rawTeam];
                        $rowTeamIds = [$rowTeamId];
                    }
                }
            }

            // Resolve Owner / Staff
            $rowOwnerBy = $defaultOwnerBy;
            if (isset($headerIndexes['owner_by'])) {
                $rawOwner = trim((string)($row[$headerIndexes['owner_by']] ?? ''));
                if ($rawOwner !== '') {
                    $cleanO = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rawOwner)));
                    $lowerEmail = strtolower($rawOwner);
                    if (isset($staffMap[$lowerEmail])) {
                        $rowOwnerBy = $staffMap[$lowerEmail];
                    } elseif (isset($staffMap[$cleanO])) {
                        $rowOwnerBy = $staffMap[$cleanO];
                    } elseif (isset($staffMap[$rawOwner])) {
                        $rowOwnerBy = $staffMap[$rawOwner];
                    }
                }
            }

            // Resolve Lead Stage from master
            $leadStageId = null;
            if (isset($headerIndexes['lead_stage'])) {
                $rawStageVal = trim((string)($row[$headerIndexes['lead_stage']] ?? ''));
                if ($rawStageVal !== '') {
                    $normStageVal = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $rawStageVal)));
                    if (isset($leadStageMap[$normStageVal])) {
                        $leadStageId = $leadStageMap[$normStageVal];
                    } elseif (isset($leadStageMap[$rawStageVal])) {
                        $leadStageId = $leadStageMap[$rawStageVal];
                    } else {
                        $matchedStage = $leadStages->first(function ($s) use ($rawStageVal) {
                            return strcasecmp($s->name, $rawStageVal) === 0 
                                || str_contains(strtolower($s->name), strtolower($rawStageVal))
                                || str_contains(strtolower($rawStageVal), strtolower($s->name));
                        });
                        if ($matchedStage) {
                            $leadStageId = $matchedStage->lead_stage_id;
                        }
                    }
                }
            }

            if ($leadStageId === null && $defaultSaleClosedId !== null) {
                $leadStageId = $defaultSaleClosedId;
            }

            $customer = Customer::withTrashed()->where('mobile', $mobileClean)->first();
            if ($customer) {
                if ($customer->trashed()) {
                    $customer->restore();
                }
                $updateData = [
                    'name'             => $name,
                    'customer_type'    => $customerType,
                    'company_name'     => $companyName,
                    'email'            => $email,
                    'alternate_mobile' => $altMobile,
                    'address'          => $address,
                    'city'             => $city,
                    'state'            => $state,
                    'country'          => $country,
                    'pincode'          => $pincode,
                ];
                if ($leadStageId !== null) {
                    $updateData['lead_stage_id'] = $leadStageId;
                }
                if ($rowBranchId !== null) {
                    $updateData['branch_id'] = $rowBranchId;
                }
                if ($rowTeamId !== null) {
                    $updateData['team_id'] = $rowTeamId;
                }
                if ($rowOwnerBy !== null) {
                    $updateData['owner_by'] = $rowOwnerBy;
                }

                $customer->update(array_filter($updateData, fn($v) => $v !== null && $v !== ''));

                // Sync pivot tables for branches and teams
                if (!empty($rowBranchIds)) {
                    $bSync = [];
                    foreach ($rowBranchIds as $bId) {
                        $bSync[$bId] = ['is_primary' => ($bId == $rowBranchId ? 1 : 0)];
                    }
                    $customer->branches()->syncWithoutDetaching($bSync);
                }
                if (!empty($rowTeamIds)) {
                    $tSync = [];
                    foreach ($rowTeamIds as $tId) {
                        $tSync[$tId] = ['is_primary' => ($tId == $rowTeamId ? 1 : 0)];
                    }
                    $customer->teams()->syncWithoutDetaching($tSync);
                }

                $updated++;
            } else {
                $createData = [
                    'name'             => $name,
                    'customer_type'    => $customerType,
                    'company_name'     => $companyName,
                    'mobile'           => $mobileClean,
                    'email'            => $email,
                    'alternate_mobile' => $altMobile,
                    'address'          => $address,
                    'city'             => $city,
                    'state'            => $state,
                    'country'          => $country ?: 'India',
                    'pincode'          => $pincode,
                    'branch_id'        => $rowBranchId,
                    'team_id'          => $rowTeamId,
                    'owner_by'         => $rowOwnerBy,
                    'status'           => 1,
                    'created_by'       => $userId,
                ];
                if ($leadStageId !== null) {
                    $createData['lead_stage_id'] = $leadStageId;
                }
                $newCustomer = Customer::create($createData);

                // Sync pivot tables for branches and teams
                if (!empty($rowBranchIds)) {
                    $bSync = [];
                    foreach ($rowBranchIds as $bId) {
                        $bSync[$bId] = ['is_primary' => ($bId == $rowBranchId ? 1 : 0)];
                    }
                    $newCustomer->branches()->syncWithoutDetaching($bSync);
                }
                if (!empty($rowTeamIds)) {
                    $tSync = [];
                    foreach ($rowTeamIds as $tId) {
                        $tSync[$tId] = ['is_primary' => ($tId == $rowTeamId ? 1 : 0)];
                    }
                    $newCustomer->teams()->syncWithoutDetaching($tSync);
                }

                $imported++;
            }
        }

        $msg = "Import completed: {$imported} new customer(s) created, {$updated} existing updated.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} invalid/empty row(s) skipped).";
        }

        return response()->json([
            'status'  => true,
            'message' => $msg,
            'details' => [
                'imported' => $imported,
                'updated'  => $updated,
                'skipped'  => $skipped,
            ]
        ]);
    }

    private function getCustomFieldsRules()
    {
        $customFields = CustomerCustomField::where('status', 1)->get();
        $rules = [];
        $attributes = [];

        foreach ($customFields as $cf) {
            $key = 'custom_fields.' . $cf->field_name;
            $fieldRules = [];

            if ($cf->is_required === 'Yes') {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($cf->field_type) {
                case 'Number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'Date':
                    $fieldRules[] = 'date';
                    break;
                case 'Dropdown':
                case 'Text':
                case 'Textarea':
                case 'Checkbox':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$key] = $fieldRules;
            $attributes[$key] = $cf->field_label;
        }

        return [$rules, $attributes];
    }

    private function processCustomFieldsPayload(array $customFieldsData = [])
    {
        $allFields = CustomerCustomField::where('status', 1)->get();
        foreach ($allFields as $field) {
            if ($field->field_type === 'Checkbox') {
                if (isset($customFieldsData[$field->field_name]) && ($customFieldsData[$field->field_name] == 1 || $customFieldsData[$field->field_name] === '1' || $customFieldsData[$field->field_name] === 'Yes')) {
                    $customFieldsData[$field->field_name] = '1';
                } else {
                    $customFieldsData[$field->field_name] = '0';
                }
            }
        }
        return $customFieldsData;
    }
}
