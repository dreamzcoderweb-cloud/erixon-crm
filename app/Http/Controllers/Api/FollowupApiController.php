<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Followup;
use App\Models\FollowupCustomField;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FollowupApiController extends Controller
{
    /**
     * Dedicated endpoint returning all form metadata, options, and defaults for Add/Edit Follow-up in mobile app.
     * Accessible via GET api/v1/followups/form-data
     */
    public function getFormData(Request $request)
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();

        // Accessible leads for the user
        $leadsQuery = Lead::query();
        if ($currentUser) {
            $leadsQuery->forUser($currentUser);
        }
        $leads = $leadsQuery->with('customer:customer_id,name,mobile')
            ->orderBy('lead_id', 'desc')
            ->get(['lead_id', 'lead_title', 'customer_id'])
            ->map(function ($lead) {
                $customerName = $lead->customer->name ?? 'N/A';
                $customerMobile = !empty($lead->customer->mobile) ? " ({$lead->customer->mobile})" : "";
                return [
                    'value' => $lead->lead_id,
                    'label' => "{$lead->lead_title} ({$customerName}{$customerMobile})",
                    'lead_title' => $lead->lead_title,
                    'customer_id' => $lead->customer_id,
                    'customer_name' => $customerName,
                ];
            });

        $staffOptions = $this->getStaffDropdownOptions();
        $customFields = $this->getFormattedCustomFields();

        return response()->json([
            'status' => true,
            'message' => 'Follow-up form data retrieved successfully.',
            'data' => [
                'leads' => $leads,
                'followup_types' => [
                    ['value' => 'Call', 'label' => 'Call'],
                    ['value' => 'Meeting', 'label' => 'Meeting'],
                    ['value' => 'Email', 'label' => 'Email'],
                    ['value' => 'WhatsApp', 'label' => 'WhatsApp'],
                    ['value' => 'Other', 'label' => 'Other'],
                ],
                'durations' => [
                    ['value' => '5 minutes', 'label' => '5 minutes'],
                    ['value' => '10 minutes', 'label' => '10 minutes'],
                    ['value' => '15 minutes', 'label' => '15 minutes'],
                    ['value' => '30 minutes', 'label' => '30 minutes'],
                    ['value' => '45 minutes', 'label' => '45 minutes'],
                    ['value' => '1 hour', 'label' => '1 hour'],
                    ['value' => '1.5 hours', 'label' => '1.5 hours'],
                    ['value' => '2 hours', 'label' => '2 hours'],
                ],
                'statuses' => [
                    ['value' => 'Pending', 'label' => 'Pending'],
                    ['value' => 'Completed', 'label' => 'Completed'],
                    ['value' => 'Cancelled', 'label' => 'Cancelled'],
                ],
                'forward_to_staff' => $staffOptions,
                'custom_fields' => $customFields,
                'defaults' => [
                    'followup_type' => 'Call',
                    'duration' => '5 minutes',
                    'followup_status' => 'Pending',
                    'forward_to' => null,
                    'created_by' => $currentUser ? $currentUser->id : null,
                    'created_by_name' => $currentUser ? $currentUser->name : null,
                ],
            ],
        ]);
    }

    /**
     * List follow-ups with filters, search, and pagination.
     * Accessible via GET api/v1/followups
     */
    public function index(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $today = Carbon::today()->toDateString();

        $query = Followup::with([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id,lead_requirement_id,lost_reason_id',
            'lead.customer:customer_id,name,mobile,email',
            'lead.leadSource:lead_sources_id,name',
            'lead.leadStage:lead_stage_id,name',
            'lead.leadRequirement:lead_requirements_id,name',
            'lead.lostReason:lost_reason_id,reason',
            'forwardToUser:id,name,email,is_on_leave',
            'creator:id,name,email',
        ])->orderBy('next_followup_date', 'asc');

        if ($user) {
            $query->forUser($user);
        }

        // Search in lead title, customer name/mobile, and remarks
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('remarks', 'like', "%{$search}%")
                    ->orWhere('followup_type', 'like', "%{$search}%")
                    ->orWhereHas('lead', function ($lq) use ($search) {
                        $lq->where('lead_title', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($cq) use ($search) {
                                $cq->where('name', 'like', "%{$search}%")
                                    ->orWhere('mobile', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter by specific Lead
        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->input('lead_id'));
        }

        // Filter by Customer
        if ($request->filled('customer_id')) {
            $query->whereHas('lead', function ($lq) use ($request) {
                $lq->where('customer_id', $request->input('customer_id'));
            });
        }

        // Filter by Lead Source
        if ($request->filled('lead_source_id')) {
            $query->whereHas('lead', function ($lq) use ($request) {
                $lq->where('lead_source_id', $request->input('lead_source_id'));
            });
        }

        // Filter by Follow-up Type (supports 'follow_type', 'followup_type', or 'type', case-insensitive, single or array/comma-separated)
        $rawType = $request->input('follow_type', $request->input('followup_type', $request->input('type')));
        $cleanTypes = [];
        if ($rawType !== null && $rawType !== '') {
            $typeList = is_array($rawType) ? $rawType : explode(',', (string) $rawType);
            $validTypeMap = [
                'call'     => 'Call',
                'meeting'  => 'Meeting',
                'email'    => 'Email',
                'whatsapp' => 'WhatsApp',
                'other'    => 'Other',
            ];
            foreach ($typeList as $t) {
                $val = trim((string) $t);
                $lowerVal = strtolower($val);
                if ($val !== '' && $lowerVal !== 'all') {
                    if (isset($validTypeMap[$lowerVal])) {
                        $cleanTypes[] = $validTypeMap[$lowerVal];
                    } else {
                        $cleanTypes[] = $val;
                    }
                }
            }
            if (!empty($cleanTypes)) {
                $query->whereIn('followup_type', $cleanTypes);
            }
        }


        // Filter by Status (handles 'status' or 'followup_status', case-insensitive)
        $statusInput = $request->input('status', $request->input('followup_status'));
        if (!empty($statusInput)) {
            $normalizedStatus = $this->normalizeStatusValue($statusInput);
            if ($normalizedStatus !== null) {
                $query->where('followup_status', $normalizedStatus);
            }
        }

        // Filter by Staff (forward_to or created_by)
        $staffId = $request->input('staff_id', $request->input('forward_to', $request->input('created_by')));
        if (!empty($staffId)) {
            $query->where(function ($q) use ($staffId) {
                $q->where('forward_to', $staffId)
                  ->orWhere('created_by', $staffId)
                  ->orWhereHas('lead', function ($lq) use ($staffId) {
                      $lq->where('assigned_to', $staffId)
                        ->orWhere('created_by', $staffId);
                  });
            });
        }

        // Date period filtering (supports from_date, to_date, start_date, end_date, etc.)
        $filterType = $request->input('filter_type', 'all');
        $customDate = $request->input('date');
        $month      = $request->input('month');
        $fromDate   = $request->input('from_date', $request->input('start_date', $request->input('from')));
        $toDate     = $request->input('to_date', $request->input('end_date', $request->input('to')));

        // Date column to filter: defaults to 'next_followup_date', or 'created_at' if specified
        $dateColumn = ($request->input('date_field') === 'created_at' || $request->input('date_column') === 'created_at')
            ? 'created_at'
            : 'next_followup_date';

        if (!empty($fromDate) || !empty($toDate)) {
            if (!empty($fromDate)) {
                $query->whereDate($dateColumn, '>=', $fromDate);
            }
            if (!empty($toDate)) {
                $query->whereDate($dateColumn, '<=', $toDate);
            }
        } elseif ($filterType === 'today') {
            $query->whereDate('next_followup_date', '=', $today);
        } elseif ($filterType === 'tomorrow' || $filterType === 'upcoming') {
            $query->whereDate('next_followup_date', '>', $today);
        } elseif ($filterType === 'overdue') {
            $query->whereDate('next_followup_date', '<', $today)
                  ->where('followup_status', 'Pending');
        } elseif ($filterType === 'daily' && !empty($customDate)) {
            $query->whereDate('next_followup_date', '=', $customDate);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($fromDate) ? Carbon::parse($fromDate) : Carbon::today();
            $query->whereBetween('next_followup_date', [
                $refDate->copy()->startOfWeek(),
                $refDate->copy()->endOfWeek(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('next_followup_date', $year ?: date('Y'))
                ->whereMonth('next_followup_date', $selectedMonth ?: date('m'));
        }

        // Additional optional created_at range filter
        if ($request->filled('created_from_date')) {
            $query->whereDate('created_at', '>=', $request->input('created_from_date'));
        }
        if ($request->filled('created_to_date')) {
            $query->whereDate('created_at', '<=', $request->input('created_to_date'));
        }

        // Calculate counts for badges/KPIs
        $baseCountQuery = Followup::query();
        if ($user) {
            $baseCountQuery->forUser($user);
        }

        $counts = [
            'all'      => (clone $baseCountQuery)->count(),
            'today'    => (clone $baseCountQuery)->whereDate('next_followup_date', '=', $today)->count(),
            'upcoming' => (clone $baseCountQuery)->whereDate('next_followup_date', '>', $today)->count(),
            'overdue'  => (clone $baseCountQuery)->whereDate('next_followup_date', '<', $today)->where('followup_status', 'Pending')->count(),
        ];

        $perPage = (int) $request->input('per_page', 20);
        $followups = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Follow-ups retrieved successfully.',
            'counts' => $counts,
            'data' => $followups->items(),
            'pagination' => [
                'total' => $followups->total(),
                'per_page' => $followups->perPage(),
                'current_page' => $followups->currentPage(),
                'last_page' => $followups->lastPage(),
            ],
        ]);
    }

    /**
     * Store a new follow-up supporting all modal fields and dynamic additional fields.
     * Accessible via POST api/v1/followups
     */
    public function store(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();

        // 1. Normalize type and status
        $followupType = $request->input('followup_type', 'Call');
        $rawStatus = $request->input('followup_status', $request->input('status', 'Pending'));
        $followupStatus = $this->normalizeStatusValue($rawStatus) ?? 'Pending';

        // 2. Extract and consolidate dynamic custom fields
        $customFieldsInput = $this->extractCustomFieldsPayload($request);
        [$customRules, $customAttributes] = $this->getCustomFieldsRules(false);

        // 3. Build validation rules
        $baseRules = [
            'lead_id'            => ['required', 'exists:leads,lead_id'],
            'followup_type'      => ['required', 'string', 'in:Call,Meeting,Email,WhatsApp,Other'],
            'duration'           => ['nullable', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ];

        if ($followupType === 'Call') {
            $baseRules['duration'] = ['required', 'string', 'max:100'];
        }

        $baseAttributes = [
            'lead_id'            => 'Lead',
            'followup_type'      => 'Follow-up Type',
            'duration'           => 'Duration',
            'remarks'            => 'Remarks / Discussion Details',
            'next_followup_date' => 'Next Follow-up Date',
            'followup_status'    => 'Status',
            'forward_to'         => 'Forward To (Staff)',
        ];

        $rules = array_merge($baseRules, $customRules);
        $attributes = array_merge($baseAttributes, $customAttributes);

        $payload = $request->all();
        $payload['followup_type'] = $followupType;
        $payload['followup_status'] = $followupStatus;
        $payload['custom_fields'] = $customFieldsInput;

        $messages = [
            'duration.required' => 'Duration is required when Follow-up Type is Call.',
        ];

        $validator = Validator::make($payload, $rules, $messages, $attributes);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // 4. Validate forward_to staff is not on leave
        if (!empty($validated['forward_to'])) {
            $forwardUser = User::find($validated['forward_to']);
            if ($forwardUser && $forwardUser->is_on_leave) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Selected staff member is currently on leave.',
                    'errors'  => [
                        'forward_to' => ['Selected staff member is currently on leave.']
                    ],
                ], 422);
            }
        }

        // Duration is only preserved for Call type
        if ($validated['followup_type'] !== 'Call') {
            $validated['duration'] = null;
        }

        $processedCustomFields = $this->processCustomFieldsPayload($customFieldsInput);

        $followup = Followup::create([
            'lead_id'            => (int) $validated['lead_id'],
            'followup_type'      => $validated['followup_type'],
            'duration'           => $validated['duration'],
            'remarks'            => !empty($validated['remarks']) ? $validated['remarks'] : null,
            'next_followup_date' => !empty($validated['next_followup_date']) ? $validated['next_followup_date'] : null,
            'followup_status'    => $followupStatus,
            'forward_to'         => !empty($validated['forward_to']) ? (int) $validated['forward_to'] : null,
            'created_by'         => $user ? $user->id : null,
            'custom_fields'      => !empty($processedCustomFields) ? $processedCustomFields : null,
        ]);

        // Sync next_followup_date to the Lead model
        if (!empty($validated['next_followup_date'])) {
            Lead::where('lead_id', $validated['lead_id'])->update([
                'next_followup_date' => $validated['next_followup_date'],
            ]);
        }

        $followup->loadMissing([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id',
            'lead.customer:customer_id,name,mobile,email',
            'forwardToUser:id,name,email,is_on_leave',
            'creator:id,name,email',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up created successfully.',
            'data'    => $followup,
        ], 201);
    }

    /**
     * Show single follow-up details by ID.
     * Accessible via GET api/v1/followups/{id}
     */
    public function show($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $followup = $this->findFollowupForUser($id, $user);

        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.',
            ], 404);
        }

        $followup->loadMissing([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id,lead_requirement_id,lost_reason_id',
            'lead.customer:customer_id,name,mobile,email',
            'lead.leadSource:lead_sources_id,name',
            'lead.leadStage:lead_stage_id,name',
            'lead.leadRequirement:lead_requirements_id,name',
            'lead.lostReason:lost_reason_id,reason',
            'forwardToUser:id,name,email,is_on_leave',
            'creator:id,name,email',
            'reassignments',
        ]);

        return response()->json([
            'status' => true,
            'data'   => $followup,
        ]);
    }

    /**
     * Fetch follow-up data along with active custom fields and options for editing.
     * Accessible via GET api/v1/followups/edit/{id}
     */
    public function edit($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $followup = $this->findFollowupForUser($id, $user);

        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.',
            ], 404);
        }

        $followup->loadMissing([
            'lead:lead_id,lead_title,customer_id',
            'lead.customer:customer_id,name,mobile,email',
            'forwardToUser:id,name,email,is_on_leave',
            'creator:id,name,email',
        ]);

        $customFields = FollowupCustomField::where('status', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $followupCustomValues = is_array($followup->custom_fields) ? $followup->custom_fields : [];

        $formattedFields = $customFields->map(function ($cf) use ($followupCustomValues) {
            $options = [];
            if (!empty($cf->field_options)) {
                $options = array_values(array_filter(array_map('trim', explode(',', $cf->field_options))));
            }

            $isRequired = in_array(strtolower((string) $cf->is_required), ['yes', '1', 'true'], true);
            $currentValue = $followupCustomValues[$cf->field_name] ?? null;

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

        // Leads options
        $leadsQuery = Lead::query();
        if ($user) {
            $leadsQuery->forUser($user);
        }
        $leads = $leadsQuery->with('customer:customer_id,name,mobile')
            ->orderBy('lead_id', 'desc')
            ->get(['lead_id', 'lead_title', 'customer_id'])
            ->map(function ($lead) {
                $customerName = $lead->customer->name ?? 'N/A';
                return [
                    'value' => $lead->lead_id,
                    'label' => "{$lead->lead_title} ({$customerName})",
                ];
            });

        $staffOptions = $this->getStaffDropdownOptions();

        return response()->json([
            'status'                   => true,
            'message'                  => 'Follow-up details for edit retrieved successfully.',
            'data'                     => $followup,
            'custom_fields_definition' => $formattedFields,
            'lead_options'             => $leads,
            'followup_type_options'    => [
                ['value' => 'Call', 'label' => 'Call'],
                ['value' => 'Meeting', 'label' => 'Meeting'],
                ['value' => 'Email', 'label' => 'Email'],
                ['value' => 'WhatsApp', 'label' => 'WhatsApp'],
                ['value' => 'Other', 'label' => 'Other'],
            ],
            'duration_options'         => [
                ['value' => '5 minutes', 'label' => '5 minutes'],
                ['value' => '10 minutes', 'label' => '10 minutes'],
                ['value' => '15 minutes', 'label' => '15 minutes'],
                ['value' => '30 minutes', 'label' => '30 minutes'],
                ['value' => '45 minutes', 'label' => '45 minutes'],
                ['value' => '1 hour', 'label' => '1 hour'],
                ['value' => '1.5 hours', 'label' => '1.5 hours'],
                ['value' => '2 hours', 'label' => '2 hours'],
            ],
            'status_options'           => [
                ['value' => 'Pending', 'label' => 'Pending'],
                ['value' => 'Completed', 'label' => 'Completed'],
                ['value' => 'Cancelled', 'label' => 'Cancelled'],
            ],
            'forward_to_options'       => $staffOptions,
        ]);
    }

    /**
     * Update an existing follow-up.
     * Accessible via POST api/v1/followups/update/{id} or PUT api/v1/followups/{id}
     */
    public function update(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $followup = $this->findFollowupForUser($id, $user);

        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.',
            ], 404);
        }

        // 1. Determine followup_type
        $followupType = $request->input('followup_type', $followup->followup_type);

        // 2. Normalize status if provided
        $followupStatus = $followup->followup_status;
        $statusInput = $request->input('followup_status', $request->input('status'));
        if (!empty($statusInput)) {
            $norm = $this->normalizeStatusValue($statusInput);
            if ($norm) {
                $followupStatus = $norm;
            }
        }

        // 3. Extract and merge custom fields
        $existingCustomFields = is_array($followup->custom_fields) ? $followup->custom_fields : [];
        $customFieldsInput = $this->extractCustomFieldsPayload($request);
        $mergedCustomFields = array_merge($existingCustomFields, $customFieldsInput);

        // 4. Build validation rules (using 'sometimes' so partial updates work safely)
        [$customRules, $customAttributes] = $this->getCustomFieldsRules(true);

        $baseRules = [
            'lead_id'            => ['sometimes', 'required', 'exists:leads,lead_id'],
            'followup_type'      => ['sometimes', 'required', 'string', 'in:Call,Meeting,Email,WhatsApp,Other'],
            'duration'           => ['nullable', 'string', 'max:100'],
            'remarks'            => ['nullable', 'string'],
            'next_followup_date' => ['nullable', 'date'],
            'followup_status'    => ['sometimes', 'required', 'in:Pending,Completed,Cancelled'],
            'forward_to'         => ['nullable', 'exists:users,id'],
        ];

        if ($followupType === 'Call') {
            $baseRules['duration'] = ['sometimes', 'required', 'string', 'max:100'];
        }

        $baseAttributes = [
            'lead_id'            => 'Lead',
            'followup_type'      => 'Follow-up Type',
            'duration'           => 'Duration',
            'remarks'            => 'Remarks / Discussion Details',
            'next_followup_date' => 'Next Follow-up Date',
            'followup_status'    => 'Status',
            'forward_to'         => 'Forward To (Staff)',
        ];

        $rules = array_merge($baseRules, $customRules);
        $attributes = array_merge($baseAttributes, $customAttributes);

        $payload = $request->all();
        if ($request->has('followup_type')) {
            $payload['followup_type'] = $followupType;
        }
        if ($request->has('followup_status') || $request->has('status')) {
            $payload['followup_status'] = $followupStatus;
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

        // Validate forward_to staff is not on leave if changed
        if ($request->filled('forward_to')) {
            $forwardUser = User::find($request->input('forward_to'));
            if ($forwardUser && $forwardUser->is_on_leave) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Selected staff member is currently on leave.',
                    'errors'  => [
                        'forward_to' => ['Selected staff member is currently on leave.']
                    ],
                ], 422);
            }
        }

        $processedCustomFields = $this->processCustomFieldsPayload($mergedCustomFields);

        $updateData = [
            'custom_fields' => !empty($processedCustomFields) ? $processedCustomFields : null,
        ];

        if ($request->has('lead_id')) {
            $updateData['lead_id'] = (int) $request->input('lead_id');
        }
        if ($request->has('followup_type')) {
            $updateData['followup_type'] = $followupType;
            if ($followupType !== 'Call') {
                $updateData['duration'] = null;
            }
        }
        if ($request->has('duration') && $followupType === 'Call') {
            $updateData['duration'] = $request->input('duration');
        }
        if ($request->has('remarks')) {
            $updateData['remarks'] = $request->input('remarks');
        }
        if ($request->has('next_followup_date')) {
            $updateData['next_followup_date'] = $request->input('next_followup_date');
        }
        if ($request->has('followup_status') || $request->has('status')) {
            $updateData['followup_status'] = $followupStatus;
        }
        if ($request->has('forward_to')) {
            $updateData['forward_to'] = $request->filled('forward_to') ? (int) $request->input('forward_to') : null;
        }

        $followup->update($updateData);

        // Sync next_followup_date to the Lead model if present
        $targetLeadId = $updateData['lead_id'] ?? $followup->lead_id;
        if (!empty($updateData['next_followup_date']) && $targetLeadId) {
            Lead::where('lead_id', $targetLeadId)->update([
                'next_followup_date' => $updateData['next_followup_date'],
            ]);
        }

        $followup->loadMissing([
            'lead:lead_id,lead_title,customer_id,lead_source_id,lead_stage_id',
            'lead.customer:customer_id,name,mobile,email',
            'forwardToUser:id,name,email,is_on_leave',
            'creator:id,name,email',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up updated successfully.',
            'data'    => $followup,
        ]);
    }

    /**
     * Delete an existing follow-up.
     * Accessible via DELETE api/v1/followups/delete/{id} or DELETE api/v1/followups/{id}
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $followup = $this->findFollowupForUser($id, $user);

        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.',
            ], 404);
        }

        $followup->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Follow-up deleted successfully.',
        ]);
    }

    /**
     * Update follow-up status (Pending, Completed, Cancelled).
     * Accessible via POST api/v1/followups/change-status/{id}
     */
    public function changeStatus(Request $request, $id)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        $followup = $this->findFollowupForUser($id, $user);

        if (!$followup) {
            return response()->json([
                'status'  => false,
                'message' => 'Follow-up not found.',
            ], 404);
        }

        $statusInput = $request->input('followup_status', $request->input('status'));
        if (!empty($statusInput)) {
            $norm = $this->normalizeStatusValue($statusInput);
            if ($norm) {
                $followup->followup_status = $norm;
            }
        } else {
            // Toggle between Completed and Pending if not specified
            $followup->followup_status = $followup->followup_status === 'Completed' ? 'Pending' : 'Completed';
        }

        $followup->save();

        return response()->json([
            'status'     => true,
            'message'    => 'Follow-up status updated successfully.',
            'new_status' => $followup->followup_status,
        ]);
    }

    /**
     * Dedicated endpoint: Get today's pending follow-ups for logged-in staff member.
     * Accessible via GET api/v1/followups/today-reminders
     */
    public function getTodayReminders(Request $request)
    {
        $user = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$user) {
            return response()->json(['status' => false, 'count' => 0, 'data' => []]);
        }

        $userId = $user->id;
        $today  = Carbon::today()->toDateString();

        $followups = Followup::with([
            'lead:lead_id,lead_title,customer_id,assigned_to',
            'lead.customer:customer_id,name,mobile,email',
            'forwardToUser:id,name,email',
            'creator:id,name,email',
        ])
        ->where('followup_status', 'Pending')
        ->whereDate('next_followup_date', '=', $today)
        ->where(function ($query) use ($userId) {
            $query->where('forward_to', $userId)
                  ->orWhere(function ($q2) use ($userId) {
                      $q2->whereNull('forward_to')
                         ->where('created_by', $userId);
                  })
                  ->orWhereHas('lead', function ($q3) use ($userId) {
                      $q3->where('assigned_to', $userId);
                  });
        })
        ->orderBy('next_followup_date', 'asc')
        ->get();

        return response()->json([
            'status' => true,
            'message' => "Today's pending follow-ups retrieved successfully.",
            'count'  => $followups->count(),
            'data'   => $followups,
        ]);
    }

    /**
     * Find follow-up accessible by user (respects staff data scopes).
     */
    private function findFollowupForUser($id, $user)
    {
        $query = Followup::query();
        if ($user) {
            $query->forUser($user);
        }
        return $query->find($id);
    }

    /**
     * Normalize status string into 'Pending', 'Completed', or 'Cancelled'.
     */
    private function normalizeStatusValue($status)
    {
        if ($status === null || $status === '') {
            return null;
        }

        $lower = strtolower(trim((string) $status));
        if ($lower === 'pending') {
            return 'Pending';
        }
        if ($lower === 'completed' || $lower === 'complete') {
            return 'Completed';
        }
        if ($lower === 'cancelled' || $lower === 'cancel') {
            return 'Cancelled';
        }

        return null;
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

        $allConfiguredFields = FollowupCustomField::where('status', 1)->get();
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
        $customFields = FollowupCustomField::where('status', 1)->get();
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
        $allFields = FollowupCustomField::where('status', 1)->get();
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
     * Get staff users dropdown options.
     */
    private function getStaffDropdownOptions()
    {
        return User::staffOnly()
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'is_on_leave'])
            ->map(function ($user) {
                $onLeaveTag = $user->is_on_leave ? ' (On Leave)' : '';
                return [
                    'value'       => $user->id,
                    'label'       => $user->name . $onLeaveTag,
                    'name'        => $user->name,
                    'email'       => $user->email,
                    'is_on_leave' => (bool) $user->is_on_leave,
                ];
            });
    }

    /**
     * Get formatted custom fields definition for mobile app / API consumers.
     */
    private function getFormattedCustomFields()
    {
        $customFields = FollowupCustomField::where('status', 1)
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
