<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\LeadRequirement;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\User;
use App\Services\AuditLogger;
use App\Traits\HasApiPermissionCheck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProposalApiController extends Controller
{
    use HasApiPermissionCheck;

    /**
     * Get Sales Manager / Staff dropdown options formatted for mobile API.
     */
    private function getSalesManagerDropdownOptions()
    {
        $salesManagers = User::role('Sales Manager')->orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        if ($salesManagers->isEmpty()) {
            $salesManagers = User::whereHas('roles', function ($q) {
                $q->where('name', 'like', '%Sales%');
            })->orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        }
        if ($salesManagers->isEmpty()) {
            $salesManagers = User::orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        }

        return $salesManagers->map(function ($sm) {
            $mobileSuffix = !empty($sm->mobile_number) ? " ({$sm->mobile_number})" : "";
            return [
                'id'            => $sm->id,
                'value'         => $sm->id,
                'name'          => $sm->name,
                'mobile_number' => $sm->mobile_number,
                'email'         => $sm->email,
                'label'         => $sm->name . $mobileSuffix,
            ];
        })->values();
    }

    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for Add/Edit Proposal in mobile app.
     * Accessible via GET api/v1/proposals/form-data
     */
    public function getFormData(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.view')) {
            return $this->permissionDeniedResponse('Proposal module');
        }

        $leadRequirements = LeadRequirement::where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['lead_requirements_id', 'name'])
            ->map(function ($req) {
                return [
                    'id'    => $req->lead_requirements_id,
                    'value' => $req->lead_requirements_id,
                    'name'  => $req->name,
                    'label' => $req->name,
                ];
            })->values();

        $salesManagers = $this->getSalesManagerDropdownOptions();

        return response()->json([
            'status'  => true,
            'message' => 'Proposal form data retrieved successfully.',
            'data'    => [
                'customer_types' => [
                    ['value' => 'user', 'label' => 'User'],
                    ['value' => 'reseller', 'label' => 'Reseller'],
                ],
                'lead_requirement_options' => $leadRequirements,
                'sales_manager_options'    => $salesManagers,
                'statuses' => [
                    ['value' => 'Active', 'label' => 'Active'],
                    ['value' => 'Inactive', 'label' => 'Inactive'],
                    ['value' => 'Converted', 'label' => 'Converted'],
                    ['value' => 'Cancelled', 'label' => 'Cancelled'],
                ],
                'defaults' => [
                    'customer_type'    => 'user',
                    'sales_manager_id' => $currentUser->id,
                    'status'           => 'Active',
                    'created_by'       => $currentUser->id,
                    'created_by_name'  => $currentUser->name,
                ],
            ],
        ]);
    }

    /**
     * Search proposals by keyword.
     * Accessible via GET api/v1/proposals/search
     */
    public function search(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.view')) {
            return $this->permissionDeniedResponse('Proposal module');
        }

        $search = trim((string) ($request->input('search') ?? $request->input('q') ?? ''));
        $query = Proposal::forUser($currentUser)->with(['items', 'customer', 'leadRequirement', 'salesManager']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('proposal_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_mobile', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('sales_manager_name', 'like', "%{$search}%")
                  ->orWhere('lead_requirement_name', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('product_package', 'like', "%{$search}%");
                  });
            });
        }

        $perPage = (int) $request->input('per_page', 20);
        $proposals = $query->orderBy('proposal_id', 'desc')->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Proposals search results.',
            'data'    => $proposals,
        ]);
    }

    /**
     * List proposals with pagination & filtering.
     * Accessible via GET api/v1/proposals
     */
    public function index(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.view')) {
            return $this->permissionDeniedResponse('Proposal module');
        }

        $query = Proposal::forUser($currentUser)->with([
            'items',
            'customer:customer_id,name,mobile,email',
            'leadRequirement:lead_requirements_id,name',
            'salesManager:id,name,mobile_number,email',
            'creator:id,name,email',
        ]);

        // General search
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('proposal_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_type', 'like', "%{$search}%")
                  ->orWhere('customer_mobile', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('sales_manager_name', 'like', "%{$search}%")
                  ->orWhere('lead_requirement_name', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('product_package', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by customer_type
        if ($request->filled('customer_type')) {
            $query->where('customer_type', strtolower(trim((string) $request->input('customer_type'))));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by Lead Requirement
        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // Filter by Sales Manager (users.id)
        if ($request->filled('sales_manager_id')) {
            $query->where('sales_manager_id', (int) $request->input('sales_manager_id'));
        }

        // Filter by Customer ID
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Date period & Range filtering (from_date / to_date)
        $filterType = $request->input('filter_type');
        $singleDate = $request->input('date');
        $month      = $request->input('month');
        $fromDate   = $request->input('from_date') ?? $request->input('start_date');
        $toDate     = $request->input('to_date') ?? $request->input('end_date');

        if (!empty($fromDate) || !empty($toDate)) {
            if (!empty($fromDate) && !empty($toDate)) {
                if ($fromDate > $toDate) {
                    [$fromDate, $toDate] = [$toDate, $fromDate];
                }
                $query->whereBetween('created_at', [
                    $fromDate . ' 00:00:00',
                    $toDate . ' 23:59:59',
                ]);
            } elseif (!empty($fromDate)) {
                $query->where('created_at', '>=', $fromDate . ' 00:00:00');
            } elseif (!empty($toDate)) {
                $query->where('created_at', '<=', $toDate . ' 23:59:59');
            }
        } elseif ($filterType === 'daily' && !empty($singleDate)) {
            $query->whereDate('created_at', $singleDate);
        } elseif (!empty($singleDate)) {
            $query->whereDate('created_at', $singleDate);
        } elseif ($filterType === 'weekly') {
            $refDate = Carbon::today();
            $query->whereBetween('created_at', [
                $refDate->copy()->startOfWeek()->toDateTimeString(),
                $refDate->copy()->endOfWeek()->toDateTimeString(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $year ?: date('Y'))
                  ->whereMonth('created_at', $selectedMonth ?: date('m'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $proposals = $query->orderBy('proposal_id', 'desc')->paginate($perPage);

        return response()->json([
            'status'  => true,
            'message' => 'Proposals retrieved successfully.',
            'data'    => $proposals,
        ]);
    }

    /**
     * Store a new proposal with product package items.
     * Accessible via POST api/v1/proposals
     */
    public function store(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.create')) {
            return $this->permissionDeniedResponse('create Proposal');
        }

        // Accept items formatted as JSON string or array
        $itemsInput = $request->input('items', []);
        if (is_string($itemsInput)) {
            $decoded = json_decode($itemsInput, true);
            $itemsInput = is_array($decoded) ? $decoded : [];
        }
        $request->merge(['items' => $itemsInput]);

        $validator = Validator::make($request->all(), [
            'customer_name'           => ['required', 'string', 'max:255'],
            'customer_type'           => ['nullable', 'string', 'max:50'],
            'customer_mobile'         => ['required', 'string', 'max:20'],
            'customer_email'          => ['nullable', 'email', 'max:255'],
            'email'                   => ['nullable', 'email', 'max:255'],
            'customer_id'             => ['nullable', 'exists:customers,customer_id'],
            'lead_requirement_id'     => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'sales_manager_id'        => ['nullable', 'exists:users,id'],
            'sales_manager_name'      => ['nullable', 'string', 'max:255'],
            'sales_manager_mobile'    => ['nullable', 'string', 'max:20'],
            'notes'                   => ['nullable', 'string'],
            'status'                  => ['nullable', 'string', 'max:30'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.product_package' => ['required', 'string', 'max:255'],
            'items.*.price'           => ['required', 'numeric', 'min:0'],
            'items.*.tax_percentage'  => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'      => ['nullable', 'numeric', 'min:0'],
            'items.*.amount'          => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            // Resolve Lead Requirement Name
            $leadRequirementName = null;
            if (!empty($validated['lead_requirement_id'])) {
                $lr = LeadRequirement::find($validated['lead_requirement_id']);
                $leadRequirementName = $lr?->name;
            }

            // Resolve Sales Manager Name & Mobile
            $salesManagerId = !empty($validated['sales_manager_id']) ? (int) $validated['sales_manager_id'] : null;
            $salesManagerName = $validated['sales_manager_name'] ?? null;
            $salesManagerMobile = $validated['sales_manager_mobile'] ?? null;

            if (!empty($salesManagerId)) {
                $sm = User::find($salesManagerId);
                if ($sm) {
                    $salesManagerName = $sm->name;
                    $salesManagerMobile = $salesManagerMobile ?: $sm->mobile_number;
                }
            }

            // Calculations
            $subtotal = 0;
            $totalTax = 0;
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $paisa = (float) ($item['price'] ?? 0);
                $taxPct = (float) ($item['tax_percentage'] ?? 0);
                $taxAmt = isset($item['tax_amount']) && $item['tax_amount'] !== ''
                    ? (float) $item['tax_amount']
                    : round(($paisa * $taxPct) / 100, 2);
                $lineAmt = isset($item['amount']) && $item['amount'] !== ''
                    ? (float) $item['amount']
                    : round($paisa + $taxAmt, 2);

                $subtotal += $paisa;
                $totalTax += $taxAmt;
                $totalAmount += $lineAmt;
            }

            $proposalNumber = $this->generateProposalNumber();
            $customerEmail = $validated['customer_email'] ?? $validated['email'] ?? null;
            $customerType = !empty($validated['customer_type']) ? strtolower($validated['customer_type']) : 'user';

            $proposal = Proposal::create([
                'proposal_number'        => $proposalNumber,
                'customer_id'            => $validated['customer_id'] ?? null,
                'customer_name'          => $validated['customer_name'],
                'customer_type'          => $customerType,
                'customer_mobile'        => $validated['customer_mobile'],
                'customer_email'         => $customerEmail,
                'lead_requirement_id'    => $validated['lead_requirement_id'] ?? null,
                'lead_requirement_name'  => $leadRequirementName,
                'sales_manager_id'       => $salesManagerId,
                'sales_manager_name'     => $salesManagerName,
                'sales_manager_mobile'   => $salesManagerMobile,
                'notes'                  => $validated['notes'] ?? null,
                'subtotal'               => round($subtotal, 2),
                'total_tax'              => round($totalTax, 2),
                'total_amount'           => round($totalAmount, 2),
                'status'                 => $validated['status'] ?? 'Active',
                'created_by'             => $currentUser?->id,
            ]);

            foreach ($validated['items'] as $item) {
                $paisa = (float) ($item['price'] ?? 0);
                $taxPct = (float) ($item['tax_percentage'] ?? 0);
                $taxAmt = isset($item['tax_amount']) && $item['tax_amount'] !== ''
                    ? (float) $item['tax_amount']
                    : round(($paisa * $taxPct) / 100, 2);
                $lineAmt = isset($item['amount']) && $item['amount'] !== ''
                    ? (float) $item['amount']
                    : round($paisa + $taxAmt, 2);

                ProposalItem::create([
                    'proposal_id'     => $proposal->proposal_id,
                    'product_package' => trim($item['product_package']),
                    'price'           => $paisa,
                    'tax_percentage'  => $taxPct,
                    'tax_amount'      => $taxAmt,
                    'amount'          => $lineAmt,
                ]);
            }

            AuditLogger::log(
                event: 'create',
                module: 'Proposal',
                description: "Proposal '{$proposal->proposal_number}' created for customer '{$proposal->customer_name}'.",
                auditable: $proposal,
                userId: $currentUser?->id
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Proposal created successfully.',
                'data'    => $proposal->load(['items', 'customer', 'leadRequirement', 'salesManager']),
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Proposal create error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to create proposal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show proposal details.
     * Accessible via GET api/v1/proposals/{id}
     */
    public function show($id, Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.view')) {
            return $this->permissionDeniedResponse('Proposal module');
        }

        $proposal = Proposal::with([
            'items',
            'customer:customer_id,name,mobile,email',
            'leadRequirement:lead_requirements_id,name',
            'salesManager:id,name,mobile_number,email',
            'creator:id,name,email',
        ])->find($id);

        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Proposal details retrieved successfully.',
            'data'    => $proposal,
        ]);
    }

    /**
     * Get proposal for editing.
     * Accessible via GET api/v1/proposals/edit/{id}
     */
    public function edit($id, Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.edit')) {
            return $this->permissionDeniedResponse('edit Proposal');
        }

        $proposal = Proposal::with('items')->find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Proposal edit data retrieved.',
            'data'    => $proposal,
        ]);
    }

    /**
     * Update an existing proposal.
     * Accessible via POST api/v1/proposals/update/{id}
     */
    public function update(Request $request, $id)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.edit')) {
            return $this->permissionDeniedResponse('edit Proposal');
        }

        $proposal = Proposal::find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        $itemsInput = $request->input('items', []);
        if (is_string($itemsInput)) {
            $decoded = json_decode($itemsInput, true);
            $itemsInput = is_array($decoded) ? $decoded : [];
        }
        $request->merge(['items' => $itemsInput]);

        $validator = Validator::make($request->all(), [
            'customer_name'           => ['required', 'string', 'max:255'],
            'customer_type'           => ['nullable', 'string', 'max:50'],
            'customer_mobile'         => ['required', 'string', 'max:20'],
            'customer_email'          => ['nullable', 'email', 'max:255'],
            'email'                   => ['nullable', 'email', 'max:255'],
            'customer_id'             => ['nullable', 'exists:customers,customer_id'],
            'lead_requirement_id'     => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'sales_manager_id'        => ['nullable', 'exists:users,id'],
            'sales_manager_name'      => ['nullable', 'string', 'max:255'],
            'sales_manager_mobile'    => ['nullable', 'string', 'max:20'],
            'notes'                   => ['nullable', 'string'],
            'status'                  => ['nullable', 'string', 'max:30'],
            'items'                   => ['required', 'array', 'min:1'],
            'items.*.product_package' => ['required', 'string', 'max:255'],
            'items.*.price'           => ['required', 'numeric', 'min:0'],
            'items.*.tax_percentage'  => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'      => ['nullable', 'numeric', 'min:0'],
            'items.*.amount'          => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        DB::beginTransaction();
        try {
            $leadRequirementName = null;
            if (!empty($validated['lead_requirement_id'])) {
                $lr = LeadRequirement::find($validated['lead_requirement_id']);
                $leadRequirementName = $lr?->name;
            }

            $salesManagerId = !empty($validated['sales_manager_id']) ? (int) $validated['sales_manager_id'] : null;
            $salesManagerName = $validated['sales_manager_name'] ?? null;
            $salesManagerMobile = $validated['sales_manager_mobile'] ?? null;

            if (!empty($salesManagerId)) {
                $sm = User::find($salesManagerId);
                if ($sm) {
                    $salesManagerName = $sm->name;
                    $salesManagerMobile = $salesManagerMobile ?: $sm->mobile_number;
                }
            }

            $subtotal = 0;
            $totalTax = 0;
            $totalAmount = 0;

            foreach ($validated['items'] as $item) {
                $paisa = (float) ($item['price'] ?? 0);
                $taxPct = (float) ($item['tax_percentage'] ?? 0);
                $taxAmt = isset($item['tax_amount']) && $item['tax_amount'] !== ''
                    ? (float) $item['tax_amount']
                    : round(($paisa * $taxPct) / 100, 2);
                $lineAmt = isset($item['amount']) && $item['amount'] !== ''
                    ? (float) $item['amount']
                    : round($paisa + $taxAmt, 2);

                $subtotal += $paisa;
                $totalTax += $taxAmt;
                $totalAmount += $lineAmt;
            }

            $customerEmail = $validated['customer_email'] ?? $validated['email'] ?? $proposal->customer_email;
            $customerType = !empty($validated['customer_type']) ? strtolower($validated['customer_type']) : ($proposal->customer_type ?: 'user');

            $proposal->update([
                'customer_id'            => $validated['customer_id'] ?? null,
                'customer_name'          => $validated['customer_name'],
                'customer_type'          => $customerType,
                'customer_mobile'        => $validated['customer_mobile'],
                'customer_email'         => $customerEmail,
                'lead_requirement_id'    => $validated['lead_requirement_id'] ?? null,
                'lead_requirement_name'  => $leadRequirementName,
                'sales_manager_id'       => $salesManagerId,
                'sales_manager_name'     => $salesManagerName,
                'sales_manager_mobile'   => $salesManagerMobile,
                'notes'                  => $validated['notes'] ?? null,
                'status'                 => $validated['status'] ?? $proposal->status,
                'subtotal'               => round($subtotal, 2),
                'total_tax'              => round($totalTax, 2),
                'total_amount'           => round($totalAmount, 2),
            ]);

            // Replace items
            $proposal->items()->delete();
            foreach ($validated['items'] as $item) {
                $paisa = (float) ($item['price'] ?? 0);
                $taxPct = (float) ($item['tax_percentage'] ?? 0);
                $taxAmt = isset($item['tax_amount']) && $item['tax_amount'] !== ''
                    ? (float) $item['tax_amount']
                    : round(($paisa * $taxPct) / 100, 2);
                $lineAmt = isset($item['amount']) && $item['amount'] !== ''
                    ? (float) $item['amount']
                    : round($paisa + $taxAmt, 2);

                ProposalItem::create([
                    'proposal_id'     => $proposal->proposal_id,
                    'product_package' => trim($item['product_package']),
                    'price'           => $paisa,
                    'tax_percentage'  => $taxPct,
                    'tax_amount'      => $taxAmt,
                    'amount'          => $lineAmt,
                ]);
            }

            AuditLogger::log(
                event: 'update',
                module: 'Proposal',
                description: "Proposal '{$proposal->proposal_number}' updated.",
                auditable: $proposal,
                userId: $currentUser?->id
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Proposal updated successfully.',
                'data'    => $proposal->load(['items', 'customer', 'leadRequirement', 'salesManager']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Proposal update error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to update proposal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete proposal (soft delete).
     * Accessible via DELETE api/v1/proposals/delete/{id} or DELETE api/v1/proposals/{id}
     */
    public function destroy($id, Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.delete')) {
            return $this->permissionDeniedResponse('delete Proposal');
        }

        $proposal = Proposal::find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        $proposalNumber = $proposal->proposal_number;

        DB::beginTransaction();
        try {
            // Soft delete child items so deleted_at is updated on proposal_items
            $proposal->items()->delete();
            $proposal->delete();

            AuditLogger::log(
                event: 'delete',
                module: 'Proposal',
                description: "Proposal '{$proposalNumber}' deleted.",
                auditable: $proposal,
                userId: $currentUser?->id
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Proposal deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API Proposal delete error: ' . $e->getMessage());

            return response()->json([
                'status'  => false,
                'message' => 'Failed to delete proposal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate / Export Proposal PDF.
     * Accessible via GET api/v1/proposals/{id}/pdf or GET api/v1/proposals/pdf/{id}
     */
    public function exportPdf($id, Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser && $request->filled('token')) {
            $token = $request->query('token');
            $tokenObj = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            if ($tokenObj) {
                $currentUser = $tokenObj->tokenable;
                Auth::setUser($currentUser);
            }
        }

        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'proposals.view')) {
            return $this->permissionDeniedResponse('view / export Proposal PDF');
        }

        $proposal = Proposal::with([
            'items',
            'customer',
            'leadRequirement',
            'salesManager',
            'creator',
        ])->find($id);

        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        // Ensure DomPDF wrapper is registered
        if (!app()->bound('dompdf.wrapper')) {
            (new \Barryvdh\DomPDF\ServiceProvider(app()))->register();
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('proposals.pdf', compact('proposal'))
            ->setPaper('a4', 'portrait');

        $fileName = 'Proposal_' . str_replace(['/', '\\'], '_', $proposal->proposal_number) . '.pdf';

        // Support JSON response with base64 for mobile apps if requested
        if ($request->input('format') === 'json' || $request->boolean('base64')) {
            $pdfContent = $pdf->output();
            $downloadUrl = url("/api/v1/proposals/{$proposal->proposal_id}/pdf?token=" . ($request->query('token') ?? ''));

            return response()->json([
                'status'          => true,
                'message'         => 'Proposal PDF generated successfully.',
                'proposal_id'     => $proposal->proposal_id,
                'proposal_number' => $proposal->proposal_number,
                'filename'        => $fileName,
                'download_url'    => $downloadUrl,
                'pdf_base64'      => base64_encode($pdfContent),
            ]);
        }

        // Stream or download
        if ($request->input('action') === 'stream' || $request->boolean('stream') || $request->input('view') === '1') {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }

    /**
     * Change proposal status.
     * Accessible via POST api/v1/proposals/change-status/{id}
     */
    public function changeStatus(Request $request, $id)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }
        if (!$this->hasPermission($currentUser, 'proposals.edit')) {
            return $this->permissionDeniedResponse('edit Proposal');
        }

        $proposal = Proposal::find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        $status = trim((string) $request->input('status', 'Active'));
        $proposal->status = $status;
        $proposal->save();

        return response()->json([
            'status'  => true,
            'message' => 'Proposal status updated successfully.',
            'data'    => [
                'proposal_id' => $proposal->proposal_id,
                'status'      => $proposal->status,
            ],
        ]);
    }

    /**
     * Helper to generate unique sequential proposal number.
     */
    private function generateProposalNumber(): string
    {
        $prefix = 'PROP-' . date('Ym') . '-';
        $last = Proposal::withTrashed()
            ->where('proposal_number', 'like', "{$prefix}%")
            ->orderBy('proposal_id', 'desc')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->proposal_number, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }
}
