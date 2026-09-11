<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CreditRequest;
use App\Models\Lead;
use App\Models\LeadCustomField;
use App\Models\LeadRequirement;
use App\Models\LeadSource;
use App\Models\LeadStage;
use App\Models\LostReason;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LeadApiController extends Controller
{
    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for Add/Edit Lead in mobile app.
     * Accessible via GET api/v1/leads/form-data
     */
    public function getFormData(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $userOptions = $this->getUserDropdownOptions();
        $customFields = $this->getFormattedCustomFields();

        $customers = Customer::query();
        if ($currentUser) {
            $customers->forUser($currentUser);
        }
        $customerOptions = $customers->where('status', 1)
            ->orderBy('name')
            ->get(['customer_id', 'name', 'mobile', 'email'])
            ->map(function ($c) {
                $extra = !empty($c->mobile) ? " ({$c->mobile})" : "";
                return [
                    'value' => $c->customer_id,
                    'label' => $c->name . $extra,
                    'name' => $c->name,
                    'mobile' => $c->mobile,
                    'email' => $c->email,
                ];
            });

        $leadsources = LeadSource::where('status', 1)
            ->orderBy('lead_sources_id', 'asc')
            ->get()
            ->map(function ($source) {
                return [
                    'value' => $source->lead_sources_id,
                    'label' => $source->name,
                ];
            });

        $leadRequirements = LeadRequirement::where('status', 1)
            ->orderBy('lead_requirements_id', 'asc')
            ->get()
            ->map(function ($req) {
                return [
                    'value' => $req->lead_requirements_id,
                    'label' => $req->name,
                ];
            });

        $leadstages = LeadStage::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('lead_stage_id', 'asc')
            ->get()
            ->map(function ($stage) {
                return [
                    'value' => $stage->lead_stage_id,
                    'label' => $stage->name,
                ];
            });

        $lostreasons = LostReason::where('status', 1)
            ->orderBy('lost_reason_id', 'asc')
            ->get()
            ->map(function ($lr) {
                return [
                    'value' => $lr->lost_reason_id,
                    'label' => $lr->reason,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Lead form data retrieved successfully.',
            'data' => [
                'customers' => $customerOptions,
                'lead_sources' => $leadsources,
                'lead_requirements' => $leadRequirements,
                'lead_stages' => $leadstages,
                'lost_reasons' => $lostreasons,
                'assigned_to_staff' => $userOptions,
                'priorities' => [
                    ['value' => 'low', 'label' => 'Low'],
                    ['value' => 'medium', 'label' => 'Medium'],
                    ['value' => 'high', 'label' => 'High'],
                    ['value' => 'urgent', 'label' => 'Urgent'],
                ],
                'statuses' => [
                    ['value' => 1, 'label' => 'Active / In-Progress'],
                    ['value' => 0, 'label' => 'Inactive / Closed'],
                ],
                'customer_types' => [
                    ['value' => 'user', 'label' => 'User'],
                    ['value' => 'reseller', 'label' => 'Reseller'],
                ],
                'custom_fields' => $customFields,
                'defaults' => [
                    'created_by' => $currentUser ? $currentUser->id : null,
                    'created_by_name' => $currentUser ? $currentUser->name : null,
                    'priority' => 'medium',
                    'status' => 1,
                    'assigned_to' => $currentUser ? $currentUser->id : null,
                ],
            ],
        ]);
    }

    /**
     * List leads with filters, search, and pagination.
     * Accessible via GET api/v1/leads
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();

        $query = Lead::with([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ])->orderBy('lead_id', 'desc');

        if ($user) {
            $query->forUser($user);
        }

        // Search across title, description, and customer details
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('lead_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filters matching Admin Leads table
        if ($request->filled('lead_title')) {
            $query->where('lead_title', $request->input('lead_title'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Filter by Lead Source (supports ID or static names e.g., 'Google', 'Referral', 'Cold Call', single or array)
        $rawSource = $request->input('lead_source_id', $request->input('lead_source', $request->input('source_id', $request->input('source'))));
        if ($rawSource !== null && $rawSource !== '') {
            $sourceList = is_array($rawSource) ? $rawSource : explode(',', (string) $rawSource);
            $cleanSources = [];
            foreach ($sourceList as $src) {
                $val = trim((string) $src);
                if ($val !== '' && strtolower($val) !== 'all') {
                    $cleanSources[] = $val;
                }
            }
            if (!empty($cleanSources)) {
                $sourceIds = [];
                $sourceNames = [];
                foreach ($cleanSources as $val) {
                    if (is_numeric($val)) {
                        $sourceIds[] = (int) $val;
                    } else {
                        $sourceObj = LeadSource::where('name', $val)
                            ->orWhere('name', 'like', "%{$val}%")
                            ->first();
                        if ($sourceObj) {
                            $sourceIds[] = $sourceObj->lead_sources_id;
                        } else {
                            $sourceNames[] = $val;
                        }
                    }
                }
                $query->where(function ($sq) use ($sourceIds, $sourceNames) {
                    if (!empty($sourceIds)) {
                        $sq->whereIn('lead_source_id', $sourceIds);
                    }
                    if (!empty($sourceNames)) {
                        foreach ($sourceNames as $name) {
                            $sq->orWhereHas('leadSource', function ($lsq) use ($name) {
                                $lsq->where('name', 'like', "%{$name}%");
                            });
                        }
                    }
                });
            }
        }

        // Filter by Lead Stage (supports ID or static names e.g., 'New', 'Contacted', 'Qualified', single or array)
        $rawStage = $request->input('lead_stage_id', $request->input('lead_stage', $request->input('stage_id', $request->input('stage'))));
        if ($rawStage !== null && $rawStage !== '') {
            $stageList = is_array($rawStage) ? $rawStage : explode(',', (string) $rawStage);
            $cleanStages = [];
            foreach ($stageList as $st) {
                $val = trim((string) $st);
                if ($val !== '' && strtolower($val) !== 'all') {
                    $cleanStages[] = $val;
                }
            }
            if (!empty($cleanStages)) {
                $stageIds = [];
                $stageNames = [];
                foreach ($cleanStages as $val) {
                    if (is_numeric($val)) {
                        $stageIds[] = (int) $val;
                    } else {
                        $stageObj = LeadStage::where('name', $val)
                            ->orWhere('name', 'like', "%{$val}%")
                            ->first();
                        if ($stageObj) {
                            $stageIds[] = $stageObj->lead_stage_id;
                        } else {
                            $stageNames[] = $val;
                        }
                    }
                }
                $query->where(function ($sq) use ($stageIds, $stageNames) {
                    if (!empty($stageIds)) {
                        $sq->whereIn('lead_stage_id', $stageIds);
                    }
                    if (!empty($stageNames)) {
                        foreach ($stageNames as $name) {
                            $sq->orWhereHas('leadStage', function ($lsq) use ($name) {
                                $lsq->where('name', 'like', "%{$name}%");
                            });
                        }
                    }
                });
            }
        }

        if ($request->filled('lead_requirement_id')) {
            $query->where('lead_requirement_id', $request->input('lead_requirement_id'));
        }

        if ($request->filled('lost_reason_id')) {
            $query->where('lost_reason_id', $request->input('lost_reason_id'));
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->input('assigned_to'));
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->input('created_by'));
        }

        // Filter by Priority (supports case-insensitive e.g. 'High', 'Medium', 'Low', ignores 'All')
        if ($request->has('priority') && $request->input('priority') !== '' && $request->input('priority') !== null) {
            $rawPriority = $request->input('priority');
            $priorityList = is_array($rawPriority) ? $rawPriority : explode(',', (string) $rawPriority);
            $validPriorities = [];
            foreach ($priorityList as $p) {
                $clean = strtolower(trim((string) $p));
                if ($clean !== '' && $clean !== 'all') {
                    $validPriorities[] = $clean;
                }
            }
            if (!empty($validPriorities)) {
                $query->whereIn('priority', $validPriorities);
            }
        }

        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $rawStatus = strtolower(trim((string) $request->input('status')));
            if ($rawStatus === 'active' || $rawStatus === '1' || $rawStatus === 'true') {
                $query->where('status', 1);
            } elseif ($rawStatus === 'inactive' || $rawStatus === 'closed' || $rawStatus === '0' || $rawStatus === 'false') {
                $query->where('status', 0);
            }
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

        $perPage = (int) $request->input('per_page', 20);
        $leads = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Leads retrieved successfully.',
            'data' => $leads->items(),
            'pagination' => [
                'total' => $leads->total(),
                'per_page' => $leads->perPage(),
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
            ],
        ]);
    }

    /**
     * Store a new lead supporting all modal fields and dynamic additional fields.
     * Accessible via POST api/v1/leads
     */
    public function store(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $isAdmin = $user ? $user->isAdmin() : false;

        // 1. Normalize priority & status
        $rawPriority = strtolower(trim((string) $request->input('priority', 'medium')));
        $priority = in_array($rawPriority, ['low', 'medium', 'high', 'urgent'], true) ? $rawPriority : 'medium';

        $rawStatus = $request->input('status', 1);
        if (is_string($rawStatus)) {
            $lowerStatus = strtolower(trim($rawStatus));
            $status = ($lowerStatus === 'inactive' || $lowerStatus === 'closed' || $lowerStatus === '0') ? 0 : 1;
        } else {
            $status = $rawStatus ? 1 : 0;
        }

        // 2. Extract and consolidate dynamic custom fields
        $customFieldsInput = $this->extractCustomFieldsPayload($request);
        [$customRules, $customAttributes] = $this->getCustomFieldsRules(false);

        // 3. Build validation rules for all Admin Lead Modal fields
        $baseRules = [
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_title'          => ['required', 'string', 'max:255'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'assigned_to'         => ['nullable', 'exists:users,id'],
            'priority'            => ['required', 'in:low,medium,high,urgent'],
            'expected_amount'     => ['nullable', 'numeric', 'min:0'],
            'description'         => ['nullable', 'string'],
            'next_followup_date'  => ['nullable', 'date'],
            'status'              => ['required', 'in:0,1'],
            'lost_reason_id'      => ['nullable', 'exists:lost_reasons,lost_reason_id'],
            'created_by'          => ['nullable', 'exists:users,id'],
        ];

        $baseAttributes = [
            'customer_id'         => 'Customer',
            'lead_title'          => 'Lead Title',
            'lead_source_id'      => 'Lead Source',
            'lead_stage_id'       => 'Lead Stage',
            'lead_requirement_id' => 'Lead Requirement',
            'assigned_to'         => 'Assigned Staff',
            'priority'            => 'Priority',
            'expected_amount'     => 'Expected Amount',
            'next_followup_date'  => 'Next Follow-up Date',
            'lost_reason_id'      => 'Lost Reason',
            'status'              => 'Status',
        ];

        $rules = array_merge($baseRules, $customRules);
        $attributes = array_merge($baseAttributes, $customAttributes);

        $payload = $request->all();
        $payload['priority'] = $priority;
        $payload['status'] = $status;
        $payload['custom_fields'] = $customFieldsInput;

        $validator = Validator::make($payload, $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Normalize checkboxes and format in custom_fields
        $processedCustomFields = $this->processCustomFieldsPayload($customFieldsInput);

        $assignedTo = !empty($validated['assigned_to']) ? (int) $validated['assigned_to'] : null;
        if (!$isAdmin && empty($assignedTo) && $user) {
            $assignedTo = $user->id;
        }

        $createdBy = ($isAdmin && !empty($validated['created_by']))
            ? (int) $validated['created_by']
            : ($user ? $user->id : null);

        $lead = Lead::create([
            'customer_id'         => (int) $validated['customer_id'],
            'lead_title'          => trim((string) $validated['lead_title']),
            'lead_source_id'      => !empty($validated['lead_source_id']) ? (int) $validated['lead_source_id'] : null,
            'lead_stage_id'       => !empty($validated['lead_stage_id']) ? (int) $validated['lead_stage_id'] : null,
            'lead_requirement_id' => !empty($validated['lead_requirement_id']) ? (int) $validated['lead_requirement_id'] : null,
            'assigned_to'         => $assignedTo,
            'priority'            => $priority,
            'expected_amount'     => isset($validated['expected_amount']) && $validated['expected_amount'] !== '' ? $validated['expected_amount'] : null,
            'description'         => !empty($validated['description']) ? $validated['description'] : null,
            'next_followup_date'  => !empty($validated['next_followup_date']) ? $validated['next_followup_date'] : null,
            'status'              => $status,
            'lost_reason_id'      => !empty($validated['lost_reason_id']) ? (int) $validated['lost_reason_id'] : null,
            'created_by'          => $createdBy,
            'custom_fields'       => !empty($processedCustomFields) ? $processedCustomFields : null,
        ]);

        // Requirement: sales closed credit request trigger
        $this->checkAndCreateSalesClosedCreditRequest($lead, $user ? $user->id : null);

        $lead->loadMissing([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Lead created successfully.',
            'data'    => $lead,
        ], 201);
    }

    /**
     * Show single lead details by ID.
     * Accessible via GET api/v1/leads/{id}
     */
    public function show($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $lead = $this->findLeadForUser($id, $user);

        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        $lead->loadMissing([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ]);

        return response()->json([
            'status' => true,
            'data'   => $lead,
        ]);
    }

    /**
     * Fetch lead data pre-filled along with active custom fields and options for editing.
     * Accessible via GET api/v1/leads/edit/{id}
     */
    public function edit($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $lead = $this->findLeadForUser($id, $user);

        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        $lead->loadMissing([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ]);

        $customFields = LeadCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $leadCustomValues = is_array($lead->custom_fields) ? $lead->custom_fields : [];

        $formattedFields = $customFields->map(function ($cf) use ($leadCustomValues) {
            $options = [];
            if (!empty($cf->field_options)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $cf->field_options))));
            }

            $isRequired = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            $currentValue = $leadCustomValues[$cf->field_name] ?? null;

            return [
                'id'            => $cf->id,
                'field_name'    => $cf->field_name,
                'field_label'   => $cf->field_label,
                'field_type'    => $cf->field_type,
                'field_options' => $options,
                'raw_options'   => $cf->field_options ?? '',
                'is_required'   => $isRequired,
                'placeholder'   => ($cf->field_type === 'Dropdown' ? 'Select ' : 'Enter ') . $cf->field_label,
                'current_value' => $currentValue,
                'sort_order'    => (int) ($cf->sort_order ?? 0),
            ];
        });

        $userOptions = $this->getUserDropdownOptions();

        $customers = Customer::query();
        if ($user) {
            $customers->forUser($user);
        }
        $customerOptions = $customers->where('status', 1)
            ->orderBy('name')
            ->get(['customer_id', 'name', 'mobile', 'email'])
            ->map(function ($c) {
                $extra = !empty($c->mobile) ? " ({$c->mobile})" : "";
                return [
                    'value' => $c->customer_id,
                    'label' => $c->name . $extra,
                    'name'  => $c->name,
                    'mobile' => $c->mobile,
                    'email' => $c->email,
                ];
            });

        $leadsources = LeadSource::where('status', 1)->orderBy('name')->get(['lead_sources_id as value', 'name as label']);
        $leadstages = LeadStage::where('status', 1)->orderBy('sort_order')->get(['lead_stage_id as value', 'name as label']);
        $leadRequirements = LeadRequirement::where('status', 1)->orderBy('name')->get(['lead_requirements_id as value', 'name as label']);
        $lostreasons = LostReason::where('status', 1)->orderBy('reason')->get(['lost_reason_id as value', 'reason as label']);

        return response()->json([
            'status'                   => true,
            'message'                  => 'Lead details for edit retrieved successfully.',
            'data'                     => $lead,
            'custom_fields_definition' => $formattedFields,
            'customer_options'         => $customerOptions,
            'lead_source_options'      => $leadsources,
            'lead_stage_options'       => $leadstages,
            'lead_requirement_options' => $leadRequirements,
            'lost_reason_options'      => $lostreasons,
            'assigned_to_options'      => $userOptions,
            'priority_options'         => [
                ['value' => 'low', 'label' => 'Low'],
                ['value' => 'medium', 'label' => 'Medium'],
                ['value' => 'high', 'label' => 'High'],
                ['value' => 'urgent', 'label' => 'Urgent'],
            ],
            'status_options'           => [
                ['value' => 1, 'label' => 'Active / In-Progress'],
                ['value' => 0, 'label' => 'Inactive / Closed'],
            ],
        ]);
    }

    /**
     * Update an existing lead.
     * Accessible via POST api/v1/leads/update/{id} or PUT api/v1/leads/{id}
     */
    public function update(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $lead = $this->findLeadForUser($id, $user);

        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        // 1. Normalize priority if provided
        $priority = $lead->priority;
        if ($request->has('priority')) {
            $rawPriority = strtolower(trim((string) $request->input('priority')));
            if (in_array($rawPriority, ['low', 'medium', 'high', 'urgent'], true)) {
                $priority = $rawPriority;
            }
        }

        // 2. Normalize status if provided
        $status = $lead->status;
        if ($request->has('status')) {
            $rawStatus = $request->input('status');
            if (is_string($rawStatus)) {
                $lowerStatus = strtolower(trim($rawStatus));
                $status = ($lowerStatus === 'inactive' || $lowerStatus === 'closed' || $lowerStatus === '0') ? 0 : 1;
            } else {
                $status = $rawStatus ? 1 : 0;
            }
        }

        // 3. Extract and merge custom fields
        $existingCustomFields = is_array($lead->custom_fields) ? $lead->custom_fields : [];
        $customFieldsInput = $this->extractCustomFieldsPayload($request);
        $mergedCustomFields = array_merge($existingCustomFields, $customFieldsInput);

        // 4. Build validation rules (using 'sometimes' so partial updates work safely)
        [$customRules, $customAttributes] = $this->getCustomFieldsRules(true);

        $baseRules = [
            'customer_id'         => ['sometimes', 'required', 'exists:customers,customer_id'],
            'lead_title'          => ['sometimes', 'required', 'string', 'max:255'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_stage_id'       => ['nullable', 'exists:lead_stages,lead_stage_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'assigned_to'         => ['nullable', 'exists:users,id'],
            'priority'            => ['sometimes', 'required', 'in:low,medium,high,urgent'],
            'expected_amount'     => ['nullable', 'numeric', 'min:0'],
            'description'         => ['nullable', 'string'],
            'next_followup_date'  => ['nullable', 'date'],
            'status'              => ['sometimes', 'required', 'in:0,1'],
            'lost_reason_id'      => ['nullable', 'exists:lost_reasons,lost_reason_id'],
        ];

        $baseAttributes = [
            'customer_id'         => 'Customer',
            'lead_title'          => 'Lead Title',
            'lead_source_id'      => 'Lead Source',
            'lead_stage_id'       => 'Lead Stage',
            'lead_requirement_id' => 'Lead Requirement',
            'assigned_to'         => 'Assigned Staff',
            'priority'            => 'Priority',
            'expected_amount'     => 'Expected Amount',
            'next_followup_date'  => 'Next Follow-up Date',
            'lost_reason_id'      => 'Lost Reason',
            'status'              => 'Status',
        ];

        $rules = array_merge($baseRules, $customRules);
        $attributes = array_merge($baseAttributes, $customAttributes);

        $payload = $request->all();
        if ($request->has('priority')) {
            $payload['priority'] = $priority;
        }
        if ($request->has('status')) {
            $payload['status'] = $status;
        }
        $payload['custom_fields'] = $mergedCustomFields;

        $validator = Validator::make($payload, $rules, [], $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $processedCustomFields = $this->processCustomFieldsPayload($mergedCustomFields);

        $updateData = [
            'custom_fields' => !empty($processedCustomFields) ? $processedCustomFields : null,
        ];

        if ($request->has('customer_id')) {
            $updateData['customer_id'] = (int) $request->input('customer_id');
        }
        if ($request->has('lead_title')) {
            $updateData['lead_title'] = trim((string) $request->input('lead_title'));
        }
        if ($request->has('lead_source_id')) {
            $updateData['lead_source_id'] = $request->filled('lead_source_id') ? (int) $request->input('lead_source_id') : null;
        }
        if ($request->has('lead_stage_id')) {
            $updateData['lead_stage_id'] = $request->filled('lead_stage_id') ? (int) $request->input('lead_stage_id') : null;
        }
        if ($request->has('lead_requirement_id')) {
            $updateData['lead_requirement_id'] = $request->filled('lead_requirement_id') ? (int) $request->input('lead_requirement_id') : null;
        }
        if ($request->has('assigned_to')) {
            $updateData['assigned_to'] = $request->filled('assigned_to') ? (int) $request->input('assigned_to') : null;
        }
        if ($request->has('priority')) {
            $updateData['priority'] = $priority;
        }
        if ($request->has('expected_amount')) {
            $updateData['expected_amount'] = $request->filled('expected_amount') ? $request->input('expected_amount') : null;
        }
        if ($request->has('description')) {
            $updateData['description'] = $request->filled('description') ? $request->input('description') : null;
        }
        if ($request->has('next_followup_date')) {
            $updateData['next_followup_date'] = $request->filled('next_followup_date') ? $request->input('next_followup_date') : null;
        }
        if ($request->has('status')) {
            $updateData['status'] = $status;
        }
        if ($request->has('lost_reason_id')) {
            $updateData['lost_reason_id'] = $request->filled('lost_reason_id') ? (int) $request->input('lost_reason_id') : null;
        }

        $lead->update($updateData);

        // Requirement: sales closed credit request trigger
        $this->checkAndCreateSalesClosedCreditRequest($lead, $user ? $user->id : null);

        $lead->loadMissing([
            'customer:customer_id,name,mobile,email',
            'leadSource:lead_sources_id,name',
            'leadStage:lead_stage_id,name',
            'leadRequirement:lead_requirements_id,name',
            'lostReason:lost_reason_id,reason',
            'assignedUser:id,name,email',
            'creator:id,name,email',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Lead updated successfully.',
            'data'    => $lead,
        ]);
    }

    /**
     * Delete an existing lead.
     * Accessible via DELETE api/v1/leads/delete/{id} or DELETE api/v1/leads/{id}
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $lead = $this->findLeadForUser($id, $user);

        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        $lead->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead deleted successfully.',
        ]);
    }

    /**
     * Toggle or update lead active/inactive status.
     * Accessible via POST api/v1/leads/change-status/{id}
     */
    public function changeStatus(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $lead = $this->findLeadForUser($id, $user);

        if (!$lead) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead not found.',
            ], 404);
        }

        if ($request->has('status')) {
            $rawStatus = $request->input('status');
            $lead->status = ($rawStatus === 1 || $rawStatus === '1' || $rawStatus === true || strtolower((string)$rawStatus) === 'active') ? 1 : 0;
        } else {
            $lead->status = $lead->status == 1 ? 0 : 1;
        }

        $lead->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Lead status updated successfully.',
            'new_status' => $lead->status,
        ]);
    }

    /**
     * Find lead accessible by the user (respects staff data scopes).
     */
    private function findLeadForUser($id, $user)
    {
        $query = Lead::query();
        if ($user) {
            $query->forUser($user);
        }
        return $query->find($id);
    }

    /**
     * Extract custom fields from either 'custom_fields' array or flat request inputs.
     */
    private function extractCustomFieldsPayload(Request $request)
    {
        $customFieldsInput = $request->input('custom_fields', []);
        if (is_string($customFieldsInput)) {
            $decoded = json_decode($customFieldsInput, true);
            $customFieldsInput = is_array($decoded) ? $decoded : [];
        } elseif (!is_array($customFieldsInput)) {
            $customFieldsInput = [];
        }

        $allConfiguredFields = LeadCustomField::where('status', 1)->get();
        foreach ($allConfiguredFields as $cf) {
            if (!isset($customFieldsInput[$cf->field_name]) && $request->has($cf->field_name)) {
                $customFieldsInput[$cf->field_name] = $request->input($cf->field_name);
            }
        }

        return $customFieldsInput;
    }

    /**
     * Build dynamic validation rules and attribute labels for custom fields.
     */
    private function getCustomFieldsRules($isUpdate = false)
    {
        $customFields = LeadCustomField::where('status', 1)->get();
        $rules = [];
        $attributes = [];

        foreach ($customFields as $cf) {
            $fieldKey = 'custom_fields.' . $cf->field_name;
            $fieldRules = [];

            if ($isUpdate) {
                $fieldRules[] = 'sometimes';
            }

            $isReq = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            if ($isReq && !$isUpdate) {
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

            $rules[$fieldKey] = $fieldRules;
            $attributes[$fieldKey] = $cf->field_label;
        }

        return [$rules, $attributes];
    }

    /**
     * Normalize custom field values (especially checkboxes).
     */
    private function processCustomFieldsPayload(array $customFieldsData)
    {
        $allFields = LeadCustomField::where('status', 1)->get();
        foreach ($allFields as $field) {
            if ($field->field_type === 'Checkbox') {
                if (isset($customFieldsData[$field->field_name])) {
                    $val = $customFieldsData[$field->field_name];
                    $customFieldsData[$field->field_name] = ($val == 1 || $val === '1' || $val === true || strtolower((string) $val) === 'yes') ? '1' : '0';
                }
            }
        }
        return $customFieldsData;
    }

    /**
     * Requirement: When lead stage is Sales Closed, create a credit request if not already present.
     */
    private function checkAndCreateSalesClosedCreditRequest(Lead $lead, $requestedBy = null)
    {
        if (!$lead->lead_stage_id) {
            return;
        }

        $stage = LeadStage::find($lead->lead_stage_id);
        if ($stage && (str_contains(strtolower($stage->name), 'closed') || str_contains(strtolower($stage->name), 'won') || str_contains(strtolower($stage->name), 'sale'))) {
            $exists = CreditRequest::where('lead_id', $lead->lead_id)->first();
            if (!$exists) {
                $customer = Customer::find($lead->customer_id);
                CreditRequest::create([
                    'lead_id'       => $lead->lead_id,
                    'customer_id'   => $lead->customer_id,
                    'username'      => $customer->name ?? null,
                    'phone'         => $customer->mobile ?? null,
                    'email'         => $customer->email ?? null,
                    'credit_amount' => $lead->expected_amount ?? 0.00,
                    'is_estimate'   => false,
                    'status'        => 'Pending Admin Approval',
                    'requested_by'  => $requestedBy ?? Auth::id(),
                ]);
            }
        }
    }

    /**
     * Get staff users dropdown options.
     */
    private function getUserDropdownOptions()
    {
        return User::staffOnly()
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->name . (!empty($user->email) ? " ({$user->email})" : ""),
                    'name'  => $user->name,
                    'email' => $user->email,
                ];
            });
    }

    /**
     * Get formatted custom fields definition for mobile app / API consumers.
     */
    private function getFormattedCustomFields()
    {
        $customFields = LeadCustomField::where('status', 1)
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
                'id'            => $cf->id,
                'field_name'    => $cf->field_name,
                'field_label'   => $cf->field_label,
                'field_type'    => $cf->field_type,
                'field_options' => $options,
                'raw_options'   => $cf->field_options ?? '',
                'is_required'   => $isRequired,
                'placeholder'   => ($cf->field_type === 'Dropdown' ? 'Select ' : 'Enter ') . $cf->field_label,
                'sort_order'    => (int) ($cf->sort_order ?? 0),
            ];
        })->values();
    }
}

