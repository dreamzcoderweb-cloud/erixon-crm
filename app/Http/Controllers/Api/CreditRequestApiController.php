<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CreditRequest;
use App\Models\CreditRequestCustomField;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadRequirement;
use App\Models\LeadSetting;
use App\Models\LeadSource;
use App\Models\User;
use App\Notifications\CreditRequestApprovedByAdmin;
use App\Notifications\CreditRequestApprovedByProductManager;
use App\Notifications\CreditRequestCreatedNotification;
use App\Traits\HasApiPermissionCheck;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CreditRequestApiController extends Controller
{
    use HasApiPermissionCheck;

    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for Add/Edit Credit Request.
     * Accessible via GET /api/v1/credit-requests/form-data
     */
    public function getFormData(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.view') && !$this->hasPermission($currentUser, 'credit-requests.create')) {
            return $this->permissionDeniedResponse('Credit Request module');
        }

        // Active Customers list
        $customerQuery = Customer::query();
        if ($currentUser && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            $customerQuery->forUser($currentUser);
        }
        $customers = $customerQuery->where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['customer_id', 'name', 'mobile', 'email', 'credit_balance'])
            ->map(function ($c) {
                $phoneSuffix = !empty($c->mobile) ? " ({$c->mobile})" : "";
                return [
                    'id'             => (int) $c->customer_id,
                    'customer_id'    => (int) $c->customer_id,
                    'value'          => (int) $c->customer_id,
                    'name'           => $c->name,
                    'mobile'         => $c->mobile,
                    'email'          => $c->email,
                    'credit_balance' => (float) ($c->credit_balance ?? 0),
                    'label'          => $c->name . $phoneSuffix,
                ];
            });

        // Active Lead Sources
        $leadSources = LeadSource::where('status', 1)
            ->orderBy('lead_sources_id', 'asc')
            ->get()
            ->map(function ($source) {
                return [
                    'id'    => (int) $source->lead_sources_id,
                    'value' => (int) $source->lead_sources_id,
                    'name'  => $source->name,
                    'label' => $source->name,
                ];
            });

        // Active Lead Requirements / Products
        $leadRequirements = LeadRequirement::where('status', 1)
            ->orderBy('lead_requirements_id', 'asc')
            ->get()
            ->map(function ($req) {
                return [
                    'id'    => (int) $req->lead_requirements_id,
                    'value' => (int) $req->lead_requirements_id,
                    'name'  => $req->name,
                    'label' => $req->name,
                ];
            });

        // Formatted Custom Fields
        $customFields = $this->getFormattedCustomFields();

        // Visible Columns configuration from LeadSetting
        $visibleColumns = $this->getVisibleColumns();

        // User Permissions for credit request actions
        $permissions = [
            'can_view'            => $this->hasPermission($currentUser, 'credit-requests.view'),
            'can_create'          => $this->hasPermission($currentUser, 'credit-requests.create'),
            'can_approve_admin'   => $currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->can('credit-requests.approve_admin'),
            'can_approve_support' => $currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->hasRole(['Product Manager', 'product manager', 'Product-Manager', 'product-manager', 'support', 'Support']) || $currentUser->can('credit-requests.approve_support'),
            'can_delete'          => $currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->can('credit-requests.delete'),
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Credit request form data retrieved successfully.',
            'data'    => [
                'customers'         => $customers,
                'lead_sources'      => $leadSources,
                'lead_requirements' => $leadRequirements,
                'custom_fields'     => $customFields,
                'status_options'    => [
                    'Pending Admin Approval',
                    'Forwarded to Product Manager',
                    'Credit Added',
                    'Rejected',
                ],
                'type_options'      => [
                    ['value' => 0, 'label' => 'Direct Credit'],
                    ['value' => 1, 'label' => 'Estimate Credit'],
                ],
                'visible_columns'   => $visibleColumns,
                'permissions'       => $permissions,
                'defaults'          => [
                    'requested_by'      => $currentUser->id,
                    'requested_by_name' => $currentUser->name,
                    'is_estimate'       => 0,
                    'status'            => 'Pending Admin Approval',
                ],
            ],
        ]);
    }

    /**
     * Get paginated list of Credit Requests with filtering, search, and summary.
     * Accessible via GET /api/v1/credit-requests
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.view')) {
            return $this->permissionDeniedResponse('Credit Request module');
        }

        $query = CreditRequest::forUser($currentUser)->with([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id,lead_requirement_id,lost_reason_id',
            'lead.customer:customer_id,name,mobile,email',
            'lead.leadStage:lead_stage_id,name',
            'lead.leadRequirement:lead_requirements_id,name',
            'lead.lostReason:lost_reason_id,reason',
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ]);

        // Status filter
        if ($request->filled('status') && strtolower($request->input('status')) !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Lead Source filter
        if ($request->filled('lead_source_id')) {
            $query->where('lead_source_id', $request->input('lead_source_id'));
        }

        // Lead Requirement filter
        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        // Is Estimate filter
        if ($request->has('is_estimate') && $request->input('is_estimate') !== '' && $request->input('is_estimate') !== null) {
            $val = $request->input('is_estimate');
            $boolVal = in_array(strtolower((string) $val), ['1', 'true', 'yes', 'estimate'], true);
            $query->where('is_estimate', $boolVal);
        }

        // Requester filter
        if ($request->filled('requested_by')) {
            $query->where('requested_by', $request->input('requested_by'));
        }

        // Search filter (customer name, phone, email, username, remarks)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('admin_remarks', 'like', "%{$search}%")
                  ->orWhere('support_remarks', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('mobile', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('requester', function ($rq) use ($search) {
                      $rq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date range & period filters (supports filter_type: daily, weekly, monthly, yearly, custom, start_date/end_date, date, month, week, year)
        $filterType = $request->input('filter_type');
        $rawStart   = $request->input('start_date') ?? $request->input('from_date');
        $rawEnd     = $request->input('end_date') ?? $request->input('to_date');
        $rawDate    = $request->input('date');
        $week       = $request->input('week');
        $month      = $request->input('month');
        $year       = $request->input('year');

        if ($filterType === 'daily' && !empty($rawDate)) {
            $query->whereDate('created_at', $rawDate);
        } elseif ($filterType === 'weekly') {
            if (!empty($week) && preg_match('/^(\d{4})-W(\d{2})$/', $week, $matches)) {
                $wYear = (int)$matches[1];
                $wWeek = (int)$matches[2];
                $startOfWeek = Carbon::now()->setISODate($wYear, $wWeek)->startOfWeek();
                $endOfWeek   = Carbon::now()->setISODate($wYear, $wWeek)->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } else {
                $refDate = !empty($rawStart) ? Carbon::parse($rawStart) : (!empty($rawDate) ? Carbon::parse($rawDate) : Carbon::today());
                $query->whereBetween('created_at', [
                    $refDate->copy()->startOfWeek(),
                    $refDate->copy()->endOfWeek(),
                ]);
            }
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$y, $m] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('created_at', $y ?: date('Y'))
                  ->whereMonth('created_at', $m ?: date('m'));
        } elseif ($filterType === 'yearly') {
            $targetYear = !empty($year) ? $year : date('Y');
            $query->whereYear('created_at', $targetYear);
        } elseif ($filterType === 'custom') {
            if (!empty($rawStart) && !empty($rawEnd)) {
                $query->whereBetween('created_at', [$rawStart . ' 00:00:00', $rawEnd . ' 23:59:59']);
            } elseif (!empty($rawStart)) {
                $query->whereDate('created_at', '>=', $rawStart);
            } elseif (!empty($rawEnd)) {
                $query->whereDate('created_at', '<=', $rawEnd);
            }
        } elseif (!empty($rawDate)) {
            $query->whereDate('created_at', $rawDate);
        } elseif (!empty($rawStart) && !empty($rawEnd)) {
            $query->whereBetween('created_at', [$rawStart . ' 00:00:00', $rawEnd . ' 23:59:59']);
        } elseif (!empty($rawStart)) {
            $query->whereDate('created_at', '>=', $rawStart);
        } elseif (!empty($rawEnd)) {
            $query->whereDate('created_at', '<=', $rawEnd);
        } elseif (!empty($month)) {
            [$yearVal, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $y = $yearVal ?: date('Y');
            $m = $selectedMonth ?: date('m');
            $query->whereYear('created_at', $y)->whereMonth('created_at', $m);
        } elseif (!empty($year)) {
            $query->whereYear('created_at', $year);
        }

        // Summary counts for current user's scope
        $summaryQuery = CreditRequest::forUser($currentUser);
        $totalCount            = (clone $summaryQuery)->count();
        $pendingAdminCount     = (clone $summaryQuery)->where('status', 'Pending Admin Approval')->count();
        $forwardedPmCount      = (clone $summaryQuery)->where('status', 'Forwarded to Product Manager')->count();
        $creditAddedCount      = (clone $summaryQuery)->where('status', 'Credit Added')->count();
        $rejectedCount         = (clone $summaryQuery)->where('status', 'Rejected')->count();
        $totalCreditAddedSum   = (clone $summaryQuery)->where('status', 'Credit Added')->sum('credit_amount');

        // Check if unpaginated list is requested
        if ($request->input('paginate') === 'false' || $request->input('all') == '1') {
            $creditRequests = $query->orderBy('credit_request_id', 'desc')->get();
            $formattedItems = $creditRequests->map(function ($cr) {
                return $this->formatCreditRequestItem($cr);
            });

            return response()->json([
                'status'  => true,
                'message' => 'Credit requests retrieved successfully.',
                'summary' => [
                    'total'                        => $totalCount,
                    'pending_admin_approval'       => $pendingAdminCount,
                    'forwarded_to_product_manager' => $forwardedPmCount,
                    'credit_added'                 => $creditAddedCount,
                    'rejected'                     => $rejectedCount,
                    'total_credit_added_amount'    => (float) $totalCreditAddedSum,
                ],
                'data'    => $formattedItems,
            ]);
        }

        $perPage = max(1, min((int) $request->input('per_page', 15), 100));
        $paginated = $query->orderBy('credit_request_id', 'desc')->paginate($perPage);

        $paginatedItems = collect($paginated->items())->map(function ($cr) {
            return $this->formatCreditRequestItem($cr);
        });

        return response()->json([
            'status'  => true,
            'message' => 'Credit requests retrieved successfully.',
            'summary' => [
                'total'                        => $totalCount,
                'pending_admin_approval'       => $pendingAdminCount,
                'forwarded_to_product_manager' => $forwardedPmCount,
                'credit_added'                 => $creditAddedCount,
                'rejected'                     => $rejectedCount,
                'total_credit_added_amount'    => (float) $totalCreditAddedSum,
            ],
            'data'    => $paginatedItems,
            'pagination' => [
                'total'        => $paginated->total(),
                'per_page'     => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
            ],
        ]);
    }

    /**
     * Store a new Credit Request.
     * Accessible via POST /api/v1/credit-requests (and POST /api/v1/credit-request)
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.create')) {
            return $this->permissionDeniedResponse('create Credit Request');
        }

        $rules = [
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'lead_id'             => ['nullable', 'exists:leads,lead_id'],
            'credit_amount'       => ['required', 'numeric', 'min:0.01'],
            'is_estimate'         => ['nullable'],
            'phone'               => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:255'],
            'custom_fields'       => ['nullable'],
        ];

        [$cfRules, $cfAttributes] = $this->getCustomFieldsRules();
        $rules = array_merge($rules, $cfRules);

        $customer = Customer::find($request->input('customer_id'));
        $customFieldsData = $this->extractCustomFieldsPayload($request, $customer);

        $payload = $request->all();
        $payload['custom_fields'] = $customFieldsData;

        $validator = Validator::make($payload, $rules);
        $validator->setAttributeNames(array_merge([
            'customer_id'   => 'Customer',
            'credit_amount' => 'Credit Amount',
        ], $cfAttributes));

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Normalize is_estimate flag
        $isEstimate = false;
        if (isset($validated['is_estimate'])) {
            $isEstimate = in_array(strtolower((string) $validated['is_estimate']), ['1', 'true', 'yes'], true);
        }

        $creditRequest = CreditRequest::create([
            'customer_id'         => $validated['customer_id'],
            'lead_id'             => $validated['lead_id'] ?? null,
            'lead_source_id'      => $validated['lead_source_id'] ?? null,
            'lead_requirement_id' => $validated['lead_requirement_id'] ?? null,
            'credit_amount'       => $validated['credit_amount'],
            'is_estimate'         => $isEstimate,
            'username'            => !empty($validated['username']) ? $validated['username'] : ($customer->name ?? null),
            'phone'               => !empty($validated['phone']) ? $validated['phone'] : ($customer->mobile ?? null),
            'email'               => !empty($validated['email']) ? $validated['email'] : ($customer->email ?? null),
            'status'              => 'Pending Admin Approval',
            'requested_by'        => $currentUser->id,
            'custom_fields'       => $customFieldsData,
        ]);

        $creditRequest->loadMissing([
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'requester:id,name',
        ]);

        // Dispatch notifications to Requester and Super Admins
        try {
            $amount = number_format((float) $creditRequest->credit_amount, 2);
            $customerName = $creditRequest->username ?? ($customer->name ?? 'Customer');
            $requesterName = $currentUser->name ?? 'Sales Staff';

            if ($currentUser) {
                $currentUser->notify(new CreditRequestCreatedNotification($creditRequest));
                $this->sendPushNotification(
                    $currentUser,
                    'Credit Request Submitted',
                    "Your credit request of ₹{$amount} for {$customerName} has been submitted (Pending Admin Approval).",
                    ['credit_request_id' => $creditRequest->credit_request_id, 'status' => $creditRequest->status]
                );
            }

            $superAdmins = User::where(function ($q) {
                $q->whereHas('roles', function ($rq) {
                    $rq->whereRaw('LOWER(name) IN (?, ?, ?)', ['super admin', 'super-admin', 'admin']);
                })->orWhere('id', 1);
            })
            ->where('id', '!=', $currentUser->id)
            ->get();

            foreach ($superAdmins as $adminUser) {
                $adminUser->notify(new CreditRequestCreatedNotification($creditRequest));
                $this->sendPushNotification(
                    $adminUser,
                    'New Credit Request Pending Approval',
                    "{$requesterName} submitted a credit request of ₹{$amount} for {$customerName} (Pending Admin Approval).",
                    ['credit_request_id' => $creditRequest->credit_request_id, 'status' => $creditRequest->status]
                );
            }
        } catch (\Throwable $e) {
            Log::error('CreditRequestApiController: Error dispatching notifications: ' . $e->getMessage());
        }

        $msgType = $isEstimate ? 'Estimate Credit Request' : 'Credit Request';

        return response()->json([
            'status'  => true,
            'message' => "{$msgType} submitted successfully (Pending Admin Approval).",
            'data'    => $this->formatCreditRequestItem($creditRequest),
        ], 201);
    }

    /**
     * Get single Credit Request record details.
     * Accessible via GET /api/v1/credit-requests/{id}
     */
    public function show($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.view')) {
            return $this->permissionDeniedResponse('Credit Request module');
        }

        $creditRequest = CreditRequest::forUser($currentUser)->with([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id,lead_requirement_id,lost_reason_id',
            'lead.customer:customer_id,name,mobile,email',
            'lead.leadStage:lead_stage_id,name',
            'lead.leadRequirement:lead_requirements_id,name',
            'lead.lostReason:lost_reason_id,reason',
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ])->find($id);

        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found or access denied.',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Credit request retrieved successfully.',
            'data'    => $this->formatCreditRequestItem($creditRequest),
        ]);
    }

    /**
     * Pre-fetch credit request record formatted for editing, along with active custom fields and options.
     * Accessible via GET /api/v1/credit-requests/edit/{id}
     */
    public function edit($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.view') && !$this->hasPermission($currentUser, 'credit-requests.create')) {
            return $this->permissionDeniedResponse('Credit Request module');
        }

        $creditRequest = CreditRequest::forUser($currentUser)->with([
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'requester:id,name',
        ])->find($id);

        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found or access denied.',
            ], 404);
        }

        $customFields = $this->getFormattedCustomFields();

        return response()->json([
            'status'  => true,
            'message' => 'Credit request edit data retrieved successfully.',
            'data'    => [
                'credit_request' => $this->formatCreditRequestItem($creditRequest),
                'custom_fields'  => $customFields,
            ],
        ]);
    }

    /**
     * Update an existing Credit Request.
     * Accessible via POST /api/v1/credit-requests/update/{id} or PUT /api/v1/credit-requests/{id}
     */
    public function update(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.create')) {
            return $this->permissionDeniedResponse('update Credit Request');
        }

        $creditRequest = CreditRequest::forUser($currentUser)->find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found or access denied.',
            ], 404);
        }

        // Prevent modification if request is already completed/Credit Added unless super admin
        if ($creditRequest->status === 'Credit Added' && !$currentUser->isAdmin() && !$currentUser->isSuperAdmin()) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit has already been added for this request and cannot be modified.',
            ], 422);
        }

        $rules = [
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'lead_id'             => ['nullable', 'exists:leads,lead_id'],
            'credit_amount'       => ['required', 'numeric', 'min:0.01'],
            'is_estimate'         => ['nullable'],
            'phone'               => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:255'],
            'custom_fields'       => ['nullable'],
        ];

        [$cfRules, $cfAttributes] = $this->getCustomFieldsRules();
        $rules = array_merge($rules, $cfRules);

        $customer = Customer::find($request->input('customer_id', $creditRequest->customer_id));
        $customFieldsData = $this->extractCustomFieldsPayload($request, $customer, $creditRequest->custom_fields ?? []);

        $payload = $request->all();
        $payload['custom_fields'] = $customFieldsData;

        $validator = Validator::make($payload, $rules);
        $validator->setAttributeNames(array_merge([
            'customer_id'   => 'Customer',
            'credit_amount' => 'Credit Amount',
        ], $cfAttributes));

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $isEstimate = $creditRequest->is_estimate;
        if (isset($validated['is_estimate'])) {
            $isEstimate = in_array(strtolower((string) $validated['is_estimate']), ['1', 'true', 'yes'], true);
        }

        $updateData = [
            'customer_id'         => $validated['customer_id'],
            'credit_amount'       => $validated['credit_amount'],
            'is_estimate'         => $isEstimate,
            'lead_source_id'      => array_key_exists('lead_source_id', $validated) ? $validated['lead_source_id'] : $creditRequest->lead_source_id,
            'lead_requirement_id' => array_key_exists('lead_requirement_id', $validated) ? $validated['lead_requirement_id'] : $creditRequest->lead_requirement_id,
            'phone'               => !empty($validated['phone']) ? $validated['phone'] : ($customer->mobile ?? $creditRequest->phone),
            'email'               => !empty($validated['email']) ? $validated['email'] : ($customer->email ?? $creditRequest->email),
            'custom_fields'       => $customFieldsData,
        ];

        if (array_key_exists('lead_id', $validated)) {
            $updateData['lead_id'] = $validated['lead_id'];
        }

        $creditRequest->update($updateData);

        $fresh = $creditRequest->fresh([
            'customer:customer_id,name,mobile,email,credit_balance',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Credit request updated successfully.',
            'data'    => $this->formatCreditRequestItem($fresh),
        ]);
    }

    /**
     * Delete (soft-delete) a Credit Request.
     * Accessible via DELETE /api/v1/credit-requests/delete/{id} or DELETE /api/v1/credit-requests/{id}
     */
    public function destroy($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        if (!$this->hasPermission($currentUser, 'credit-requests.delete')) {
            return $this->permissionDeniedResponse('delete Credit Request');
        }

        $creditRequest = CreditRequest::forUser($currentUser)->find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found or access denied.',
            ], 404);
        }

        $creditRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Credit request deleted successfully.',
        ]);
    }

    /**
     * Admin Approval -> Status: Forwarded to Product Manager.
     * Accessible via POST /api/v1/credit-requests/approve-admin/{id}
     */
    public function approveAdmin(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = $currentUser->isAdmin() || $currentUser->isSuperAdmin() || $currentUser->can('credit-requests.approve_admin');
        if (!$isAdmin) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action. Admin approval permission required.',
            ], 403);
        }

        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found.',
            ], 404);
        }

        $creditRequest->status            = 'Forwarded to Product Manager';
        $creditRequest->admin_approved_by = $currentUser->id;
        $creditRequest->admin_approved_at = now();
        $creditRequest->admin_remarks     = $request->input('admin_remarks') ?? $request->input('remarks');
        $creditRequest->save();

        // Dispatch notifications to Approver, Product Managers, and Requester
        try {
            $recipients = collect();
            if ($currentUser) {
                $recipients->push($currentUser);
            }

            $productManagers = User::whereHas('roles', function ($query) {
                $query->whereRaw('LOWER(name) IN (?, ?, ?)', ['product manager', 'product-manager', 'product_manager']);
            })->get();

            foreach ($productManagers as $pm) {
                $recipients->push($pm);
            }

            if (!empty($creditRequest->requested_by)) {
                $requester = User::find($creditRequest->requested_by);
                if ($requester) {
                    $recipients->push($requester);
                }
            }

            $recipients = $recipients->unique('id');
            $amount = number_format((float) $creditRequest->credit_amount, 2);
            $customerName = $creditRequest->username ?? ($creditRequest->customer->name ?? 'Customer');

            foreach ($recipients as $recipient) {
                $recipient->notify(new CreditRequestApprovedByAdmin($creditRequest));

                $isRequester = ($recipient->id == $creditRequest->requested_by);
                $title = 'Credit Request Approved by Admin';
                $body = $isRequester
                    ? "Your credit request of ₹{$amount} for {$customerName} has been approved by Super Admin and forwarded to Product Manager."
                    : "Super Admin approved credit request of ₹{$amount} for {$customerName}. Next, Product Manager approval is required.";

                $this->sendPushNotification(
                    $recipient,
                    $title,
                    $body,
                    ['credit_request_id' => $creditRequest->credit_request_id, 'status' => $creditRequest->status]
                );
            }
        } catch (\Throwable $e) {
            Log::error('CreditRequestApiController: Error in approveAdmin notifications: ' . $e->getMessage());
        }

        $fresh = $creditRequest->fresh([
            'customer:customer_id,name,mobile,email,credit_balance',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Credit request approved by Admin and forwarded to Product Manager.',
            'data'    => $this->formatCreditRequestItem($fresh),
        ]);
    }

    /**
     * Support Team / Product Manager Approval -> Credit Added to Customer Balance.
     * Accessible via POST /api/v1/credit-requests/approve-support/{id}
     */
    public function approveSupport(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isAuthorized = $currentUser->isAdmin()
            || $currentUser->isSuperAdmin()
            || $currentUser->hasRole(['Product Manager', 'product manager', 'Product-Manager', 'product-manager', 'support', 'Support'])
            || $currentUser->can('credit-requests.approve_support');

        if (!$isAuthorized) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action. Product Manager or Support Team permission required.',
            ], 403);
        }

        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found.',
            ], 404);
        }

        if ($creditRequest->status === 'Credit Added') {
            return response()->json([
                'status'  => false,
                'message' => 'Credit has already been added for this request.',
            ], 422);
        }

        $creditRequest->status              = 'Credit Added';
        $creditRequest->support_approved_by = $currentUser->id;
        $creditRequest->support_approved_at = now();
        $creditRequest->support_remarks     = $request->input('support_remarks') ?? $request->input('remarks');
        $creditRequest->save();

        // Increment customer credit_balance
        $customer = Customer::find($creditRequest->customer_id);
        if ($customer) {
            $customer->credit_balance = floatval($customer->credit_balance ?? 0) + floatval($creditRequest->credit_amount);
            $customer->save();
        }

        // Dispatch notifications to Product Manager, Admin, and Requester
        try {
            $recipients = collect();
            if ($currentUser) {
                $recipients->push($currentUser);
            }

            $adminId = $creditRequest->admin_approved_by ?: 1;
            $adminUser = User::find($adminId);
            if ($adminUser) {
                $recipients->push($adminUser);
            }

            if (!empty($creditRequest->requested_by)) {
                $requester = User::find($creditRequest->requested_by);
                if ($requester) {
                    $recipients->push($requester);
                }
            }

            $recipients = $recipients->unique('id');
            $amount = number_format((float) $creditRequest->credit_amount, 2);
            $customerName = $creditRequest->username ?? ($creditRequest->customer->name ?? 'Customer');

            foreach ($recipients as $recipient) {
                $recipient->notify(new CreditRequestApprovedByProductManager($creditRequest));

                $isRequester = ($recipient->id == $creditRequest->requested_by);
                $title = $isRequester ? 'Credit Request Completed' : 'Credit Request Approved by Product Manager';
                $body = $isRequester
                    ? "Your credit request of ₹{$amount} for {$customerName} has been approved by Product Manager and added to customer credit balance."
                    : "Product Manager has approved the credit request of ₹{$amount} for {$customerName}. Credit has been added to customer balance.";

                $this->sendPushNotification(
                    $recipient,
                    $title,
                    $body,
                    ['credit_request_id' => $creditRequest->credit_request_id, 'status' => $creditRequest->status]
                );
            }
        } catch (\Throwable $e) {
            Log::error('CreditRequestApiController: Error in approveSupport notifications: ' . $e->getMessage());
        }

        $fresh = $creditRequest->fresh([
            'customer:customer_id,name,mobile,email,credit_balance',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Support team approved credit request. Added ₹' . number_format($creditRequest->credit_amount, 2) . ' to customer credit balance.',
            'data'    => $this->formatCreditRequestItem($fresh),
        ]);
    }

    /**
     * Reject a Credit Request.
     * Accessible via POST /api/v1/credit-requests/reject/{id}
     */
    public function reject(Request $request, $id): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $isAuthorized = $currentUser->isAdmin()
            || $currentUser->isSuperAdmin()
            || $currentUser->can('credit-requests.approve_admin')
            || $currentUser->can('credit-requests.approve_support');

        if (!$isAuthorized) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized action. Approver permission required to reject requests.',
            ], 403);
        }

        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json([
                'status'  => false,
                'message' => 'Credit request not found.',
            ], 404);
        }

        $creditRequest->status        = 'Rejected';
        $creditRequest->admin_remarks = $request->input('remarks') ?? $request->input('admin_remarks', 'Request rejected.');
        $creditRequest->save();

        $fresh = $creditRequest->fresh([
            'customer:customer_id,name,mobile,email,credit_balance',
            'adminApprover:id,name',
            'supportApprover:id,name',
            'requester:id,name',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Credit request rejected.',
            'data'    => $this->formatCreditRequestItem($fresh),
        ]);
    }

    /**
     * Mobile convenience endpoint to change status of a credit request.
     * Accessible via POST /api/v1/credit-requests/change-status/{id}
     */
    public function changeStatus(Request $request, $id): JsonResponse
    {
        $newStatus = $request->input('status');
        $validStatuses = [
            'Pending Admin Approval',
            'Forwarded to Product Manager',
            'Credit Added',
            'Rejected',
        ];

        if (!in_array($newStatus, $validStatuses, true)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid status provided. Valid statuses are: ' . implode(', ', $validStatuses),
            ], 422);
        }

        if ($newStatus === 'Forwarded to Product Manager') {
            return $this->approveAdmin($request, $id);
        }

        if ($newStatus === 'Credit Added') {
            return $this->approveSupport($request, $id);
        }

        if ($newStatus === 'Rejected') {
            return $this->reject($request, $id);
        }

        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $creditRequest = CreditRequest::find($id);
        if (!$creditRequest) {
            return response()->json(['status' => false, 'message' => 'Credit request not found.'], 404);
        }

        $creditRequest->status = $newStatus;
        $creditRequest->save();

        return response()->json([
            'status'     => true,
            'message'    => "Credit request status updated to {$newStatus}.",
            'new_status' => $newStatus,
            'data'       => $this->formatCreditRequestItem($creditRequest->fresh()),
        ]);
    }

    /**
     * Format a CreditRequest item for consistent API responses.
     */
    private function formatCreditRequestItem(CreditRequest $cr): array
    {
        $statusBadges = [
            'Pending Admin Approval'       => 'warning',
            'Forwarded to Product Manager' => 'info',
            'Credit Added'                 => 'success',
            'Rejected'                     => 'danger',
        ];

        return [
            'credit_request_id'           => (int) $cr->credit_request_id,
            'id'                          => (int) $cr->credit_request_id,
            'customer_id'                 => $cr->customer_id ? (int) $cr->customer_id : null,
            'customer'                    => $cr->customer ? [
                'customer_id'    => (int) $cr->customer->customer_id,
                'name'           => $cr->customer->name,
                'mobile'         => $cr->customer->mobile,
                'email'          => $cr->customer->email,
                'credit_balance' => (float) ($cr->customer->credit_balance ?? 0),
            ] : null,
            'customer_name'               => $cr->username ?? ($cr->customer ? $cr->customer->name : 'N/A'),
            'phone'                       => $cr->phone ?? ($cr->customer ? $cr->customer->mobile : null),
            'email'                       => $cr->email ?? ($cr->customer ? $cr->customer->email : null),
            'lead_id'                     => $cr->lead_id ? (int) $cr->lead_id : null,
            'lead_title'                  => $cr->lead ? $cr->lead->lead_title : null,
            'lead_source_id'              => $cr->lead_source_id ? (int) $cr->lead_source_id : null,
            'lead_source'                 => $cr->leadSource ? $cr->leadSource->name : 'N/A',
            'lead_requirement_id'         => $cr->lead_requirement_id ? (int) $cr->lead_requirement_id : null,
            'lead_requirement'            => $cr->leadRequirement ? $cr->leadRequirement->name : 'N/A',
            'credit_amount'               => (float) $cr->credit_amount,
            'credit_amount_formatted'     => '₹' . number_format((float) $cr->credit_amount, 2),
            'is_estimate'                 => (bool) $cr->is_estimate,
            'type_label'                  => $cr->is_estimate ? 'Estimate Credit' : 'Direct Credit',
            'status'                      => $cr->status,
            'status_badge'                => $statusBadges[$cr->status] ?? 'secondary',
            'admin_approved_by'           => $cr->admin_approved_by ? (int) $cr->admin_approved_by : null,
            'admin_approver_name'         => $cr->adminApprover ? $cr->adminApprover->name : null,
            'admin_approved_at'           => $cr->admin_approved_at ? $cr->admin_approved_at->format('Y-m-d H:i:s') : null,
            'admin_approved_at_formatted' => $cr->admin_approved_at ? $cr->admin_approved_at->format('d M Y, h:i A') : null,
            'admin_remarks'               => $cr->admin_remarks,
            'support_approved_by'         => $cr->support_approved_by ? (int) $cr->support_approved_by : null,
            'support_approver_name' => $cr->supportApprover ? $cr->supportApprover->name : null,
            'support_approved_at'   => $cr->support_approved_at ? $cr->support_approved_at->format('Y-m-d H:i:s') : null,
            'support_approved_at_formatted' => $cr->support_approved_at ? $cr->support_approved_at->format('d M Y, h:i A') : null,
            'support_remarks'             => $cr->support_remarks,
            'requested_by'                => $cr->requested_by ? (int) $cr->requested_by : null,
            'requester_name'              => $cr->requester ? $cr->requester->name : null,
            'custom_fields'               => $cr->custom_fields ?? [],
            'created_at'                  => $cr->created_at ? $cr->created_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted'        => $cr->created_at ? $cr->created_at->format('d M Y, h:i A') : null,
            'updated_at'                  => $cr->updated_at ? $cr->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Get formatted credit request custom fields definitions.
     */
    private function getFormattedCustomFields()
    {
        $customFields = CreditRequestCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return $customFields->map(function ($cf) {
            $options = [];
            if (!empty($cf->field_options)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $cf->field_options))));
            }

            $isRequired = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);

            return [
                'id'            => (int) $cf->id,
                'field_name'    => $cf->field_name,
                'field_label'   => $cf->field_label,
                'field_type'    => $cf->field_type, // 'Text', 'Number', 'Dropdown', 'Textarea', 'Date', 'Checkbox'
                'field_options' => $options,
                'raw_options'   => $cf->field_options ?? '',
                'is_required'   => $isRequired,
                'placeholder'   => ($cf->field_type === 'Dropdown' ? 'Select ' : 'Enter ') . $cf->field_label,
                'sort_order'    => (int) ($cf->sort_order ?? 0),
            ];
        })->values();
    }

    /**
     * Get dynamic validation rules from CreditRequestCustomField.
     */
    private function getCustomFieldsRules(): array
    {
        $customFields = CreditRequestCustomField::where('status', 1)->get();
        $rules = [];
        $attributes = [];

        foreach ($customFields as $field) {
            $ruleKey = 'custom_fields.' . $field->field_name;
            $fieldRules = [];

            $isReq = in_array(strtolower((string) $field->is_required), ['yes', '1', 'true'], true);
            if ($isReq) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            switch ($field->field_type) {
                case 'Number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'Date':
                    $fieldRules[] = 'date';
                    break;
                case 'Text':
                case 'Textarea':
                case 'Dropdown':
                case 'Checkbox':
                default:
                    $fieldRules[] = 'string';
                    break;
            }

            $rules[$ruleKey] = $fieldRules;
            $attributes[$ruleKey] = $field->field_label;
        }

        return [$rules, $attributes];
    }

    /**
     * Extract and consolidate custom fields from payload or top-level request parameters.
     */
    private function extractCustomFieldsPayload(Request $request, ?Customer $customer = null, array $existingCustomFields = []): array
    {
        $customFieldsInput = $request->input('custom_fields', $existingCustomFields);
        if (is_string($customFieldsInput)) {
            $decoded = json_decode($customFieldsInput, true);
            $customFieldsInput = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($customFieldsInput)) {
            $customFieldsInput = [];
        }

        $allFields = CreditRequestCustomField::where('status', 1)->get();
        foreach ($allFields as $field) {
            // Check top-level request parameters or fallback to customer details
            if (!isset($customFieldsInput[$field->field_name]) || $customFieldsInput[$field->field_name] === '') {
                if ($request->filled($field->field_name)) {
                    $customFieldsInput[$field->field_name] = $request->input($field->field_name);
                } elseif ($field->field_name === 'username' && $customer && !empty($customer->name)) {
                    $customFieldsInput[$field->field_name] = $customer->name;
                } elseif ($field->field_name === 'phone' && $customer && !empty($customer->mobile)) {
                    $customFieldsInput[$field->field_name] = $customer->mobile;
                } elseif ($field->field_name === 'email' && $customer && !empty($customer->email)) {
                    $customFieldsInput[$field->field_name] = $customer->email;
                }
            }

            if ($field->field_type === 'Checkbox') {
                if (isset($customFieldsInput[$field->field_name])) {
                    $val = $customFieldsInput[$field->field_name];
                    $customFieldsInput[$field->field_name] = ($val == 1 || $val === '1' || $val === true || strtolower((string) $val) === 'yes') ? '1' : '0';
                } else {
                    $customFieldsInput[$field->field_name] = '0';
                }
            }
        }

        return $customFieldsInput;
    }

    /**
     * Sanitize and process custom fields input payload.
     */
    private function processCustomFieldsPayload($customFieldsInput): array
    {
        if (is_string($customFieldsInput)) {
            $decoded = json_decode($customFieldsInput, true);
            $customFieldsData = is_array($decoded) ? $decoded : [];
        } elseif (is_array($customFieldsInput)) {
            $customFieldsData = $customFieldsInput;
        } else {
            $customFieldsData = [];
        }

        $allFields = CreditRequestCustomField::where('status', 1)->get();
        foreach ($allFields as $field) {
            // Check top-level request parameters as fallback
            if (!isset($customFieldsData[$field->field_name]) && request()->has($field->field_name)) {
                $customFieldsData[$field->field_name] = request()->input($field->field_name);
            }

            if ($field->field_type === 'Checkbox') {
                if (isset($customFieldsData[$field->field_name])) {
                    $val = $customFieldsData[$field->field_name];
                    $customFieldsData[$field->field_name] = ($val == 1 || $val === '1' || $val === true || strtolower((string) $val) === 'yes') ? '1' : '0';
                } else {
                    $customFieldsData[$field->field_name] = '0';
                }
            }
        }

        return $customFieldsData;
    }

    /**
     * Get configured visible columns.
     */
    private function getVisibleColumns(): array
    {
        $standardFields = [
            'customer_info'    => 'Customer / User',
            'contact_info'     => 'Phone / Email',
            'lead_source'      => 'Lead Source',
            'lead_stage'       => 'Lead Stages',
            'lead_requirement' => 'Lead Requirements',
            'lost_reason'      => 'Lost Reason',
            'credit_amount'    => 'Credit Amount',
            'is_estimate'      => 'Type',
            'status'           => 'Status',
            'requested_by'     => 'Requested By',
            'created_at'       => 'Date',
        ];

        $allAvailableFieldsMap = [];
        foreach ($standardFields as $key => $label) {
            $allAvailableFieldsMap[$key] = [
                'key'   => $key,
                'label' => $label,
                'type'  => 'standard',
            ];
        }

        $customFields = CreditRequestCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($customFields as $cf) {
            $allAvailableFieldsMap[$cf->field_name] = [
                'key'   => $cf->field_name,
                'label' => $cf->field_label,
                'type'  => 'custom',
            ];
        }

        $setting = LeadSetting::getSettings();
        $savedColumns = $setting->credit_request_list_columns ?? null;

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

        return $visibleColumns;
    }

    /**
     * Send FCM Push Notification safely if user has registered fcmtoken.
     */
    private function sendPushNotification(User $user, string $title, string $message, array $data = []): void
    {
        if (!empty($user->fcmtoken)) {
            try {
                $fcm = app(\App\Services\FirebaseNotificationService::class);
                $fcm->sendNotification($user->fcmtoken, $title, $message, $data);
            } catch (\Throwable $e) {
                Log::warning('CreditRequestApiController: FCM push delivery error: ' . $e->getMessage());
            }
        }
    }
}
