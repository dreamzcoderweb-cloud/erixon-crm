<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LeadRequirement;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\User;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProposalController extends Controller
{
    /**
     * Display a listing of proposals or return view with dropdown datasets.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        $data['customers'] = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();
        $data['leadRequirements'] = LeadRequirement::where('status', 1)->orderBy('name')->get();

        // Fetch Sales Managers with role 'Sales Manager'
        $salesManagers = User::role('Sales Manager')->orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        if ($salesManagers->isEmpty()) {
            $salesManagers = User::whereHas('roles', function ($q) {
                $q->where('name', 'like', '%Sales%');
            })->orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        }
        if ($salesManagers->isEmpty()) {
            $salesManagers = User::orderBy('name')->get(['id', 'name', 'mobile_number', 'email']);
        }
        $data['salesManagers'] = $salesManagers;

        return view('proposals.view', $data);
    }

    /**
     * AJAX DataTable data endpoint for Proposals list.
     */
    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = Auth::user();

        $query = Proposal::forUser($user)->with([
            'customer:customer_id,name,mobile,email',
            'leadRequirement:lead_requirements_id,name',
            'salesManager:id,name,mobile_number,email',
            'creator:id,name,email',
            'items',
        ]);

        // General search
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('proposal_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_mobile', 'like', "%{$search}%")
                  ->orWhere('sales_manager_name', 'like', "%{$search}%")
                  ->orWhere('lead_requirement_name', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('product_package', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Lead Requirement
        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // Filter by Sales Manager
        if ($request->filled('sales_manager_id')) {
            $query->where('sales_manager_id', $request->input('sales_manager_id'));
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Date period filtering
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
                $refDate->copy()->startOfWeek()->toDateTimeString(),
                $refDate->copy()->endOfWeek()->toDateTimeString(),
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

        $proposals = $query->orderBy('proposal_id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data'   => $proposals,
        ]);
    }

    /**
     * Store a newly created proposal with multiple product packages.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'          => ['required', 'string', 'max:255'],
            'customer_mobile'        => ['required', 'string', 'max:20'],
            'customer_id'            => ['nullable', 'exists:customers,customer_id'],
            'lead_requirement_id'    => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'sales_manager_id'       => ['nullable', 'exists:users,id'],
            'sales_manager_name'     => ['nullable', 'string', 'max:255'],
            'sales_manager_mobile'   => ['nullable', 'string', 'max:20'],
            'notes'                  => ['nullable', 'string'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_package'=> ['required', 'string', 'max:255'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
            'items.*.tax_percentage' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'     => ['nullable', 'numeric', 'min:0'],
            'items.*.amount'         => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

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
            } elseif (!empty($salesManagerName)) {
                $sm = User::where('name', $salesManagerName)->first();
                if ($sm) {
                    $salesManagerId = $sm->id;
                    $salesManagerMobile = $salesManagerMobile ?: $sm->mobile_number;
                }
            }

            // Calculate Subtotal, Total Tax, and Total Amount
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

            // Unique proposal number generation
            $proposalNumber = $this->generateProposalNumber();

            $proposal = Proposal::create([
                'proposal_number'        => $proposalNumber,
                'customer_id'            => $validated['customer_id'] ?? null,
                'customer_name'          => $validated['customer_name'],
                'customer_mobile'        => $validated['customer_mobile'],
                'lead_requirement_id'    => $validated['lead_requirement_id'] ?? null,
                'lead_requirement_name'  => $leadRequirementName,
                'sales_manager_id'       => $salesManagerId,
                'sales_manager_name'     => $salesManagerName,
                'sales_manager_mobile'   => $salesManagerMobile,
                'notes'                  => $validated['notes'] ?? null,
                'subtotal'               => round($subtotal, 2),
                'total_tax'              => round($totalTax, 2),
                'total_amount'           => round($totalAmount, 2),
                'status'                 => 'Active',
                'created_by'             => $user?->id,
            ]);

            // Save individual items
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
                description: "Proposal '{$proposal->proposal_number}' created for customer '{$proposal->customer_name}' with amount ₹{$proposal->total_amount}.",
                auditable: $proposal,
                userId: $user?->id
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Proposal created successfully.',
                'data'    => $proposal->load(['items', 'customer', 'leadRequirement', 'salesManager']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to create proposal: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to create proposal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show proposal details (used for View Modal & Detail retrieval).
     */
    public function show($id)
    {
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
            'status' => true,
            'data'   => $proposal,
        ]);
    }

    /**
     * Get proposal for editing.
     */
    public function edit($id)
    {
        $proposal = Proposal::with('items')->find($id);

        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        // Auto-link sales_manager_id and mobile if missing on legacy/existing records
        if (empty($proposal->sales_manager_id) && !empty($proposal->sales_manager_name)) {
            $sm = User::where('name', $proposal->sales_manager_name)->first();
            if ($sm) {
                $proposal->sales_manager_id = $sm->id;
                if (empty($proposal->sales_manager_mobile)) {
                    $proposal->sales_manager_mobile = $sm->mobile_number;
                }
                $proposal->save();
            }
        }

        return response()->json([
            'status' => true,
            'data'   => $proposal,
        ]);
    }

    /**
     * Update an existing proposal.
     */
    public function update(Request $request, $id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        $validated = $request->validate([
            'customer_name'          => ['required', 'string', 'max:255'],
            'customer_mobile'        => ['required', 'string', 'max:20'],
            'customer_id'            => ['nullable', 'exists:customers,customer_id'],
            'lead_requirement_id'    => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'sales_manager_id'       => ['nullable', 'exists:users,id'],
            'sales_manager_name'     => ['nullable', 'string', 'max:255'],
            'sales_manager_mobile'   => ['nullable', 'string', 'max:20'],
            'notes'                  => ['nullable', 'string'],
            'status'                 => ['nullable', 'string', 'max:30'],
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_package'=> ['required', 'string', 'max:255'],
            'items.*.price'          => ['required', 'numeric', 'min:0'],
            'items.*.tax_percentage' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount'     => ['nullable', 'numeric', 'min:0'],
            'items.*.amount'         => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();

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
                    $salesManagerMobile = $sm->mobile_number;
                }
            } elseif (!empty($salesManagerName)) {
                $sm = User::where('name', $salesManagerName)->first();
                if ($sm) {
                    $salesManagerId = $sm->id;
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

            $proposal->update([
                'customer_id'            => $validated['customer_id'] ?? null,
                'customer_name'          => $validated['customer_name'],
                'customer_mobile'        => $validated['customer_mobile'],
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
                userId: $user?->id
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Proposal updated successfully.',
                'data'    => $proposal->load(['items', 'customer', 'leadRequirement', 'salesManager']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Failed to update proposal: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'status'  => false,
                'message' => 'Failed to update proposal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete proposal.
     */
    public function destroy($id)
    {
        $proposal = Proposal::find($id);
        if (!$proposal) {
            return response()->json(['status' => false, 'message' => 'Proposal not found.'], 404);
        }

        $proposalNumber = $proposal->proposal_number;
        $proposal->delete();

        AuditLogger::log(
            event: 'delete',
            module: 'Proposal',
            description: "Proposal '{$proposalNumber}' deleted.",
            auditable: $proposal,
            userId: Auth::id()
        );

        return response()->json([
            'status'  => true,
            'message' => 'Proposal deleted successfully.',
        ]);
    }

    /**
     * Print-friendly proposal view.
     */
    public function print($id)
    {
        $proposal = Proposal::with([
            'items',
            'customer',
            'leadRequirement',
            'salesManager',
            'creator',
        ])->findOrFail($id);

        return view('proposals.print', compact('proposal'));
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
