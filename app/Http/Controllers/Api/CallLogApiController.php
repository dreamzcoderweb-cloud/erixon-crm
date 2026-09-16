<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\CallRecording;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CallLogApiController extends Controller
{
    /**
     * Get list of call logs formatted with mobile parameters.
     * GET /api/v1/call-logs
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $query = CallLog::forUser($currentUser)
            ->with(['customer', 'followup', 'lead', 'user', 'recording']);

        // Filter by phone number
        if ($request->filled('phone_number') || $request->filled('phone')) {
            $phone = trim($request->input('phone_number') ?? $request->input('phone'));
            $query->where('phone', 'LIKE', "%{$phone}%");
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $rawCustId = $request->input('customer_id');
            $cleanCustId = preg_replace('/\D/', '', (string) $rawCustId);
            $query->where(function ($q) use ($rawCustId, $cleanCustId) {
                $q->where('customer_code', $rawCustId);
                if (!empty($cleanCustId)) {
                    $q->orWhere('customer_id', $cleanCustId);
                }
            });
        }

        // Filter by lead
        if ($request->filled('lead_id')) {
            $rawLeadId = $request->input('lead_id');
            $cleanLeadId = preg_replace('/\D/', '', (string) $rawLeadId);
            if (!empty($cleanLeadId)) {
                $query->where('lead_id', $cleanLeadId);
            }
        }

        // Filter by status
        if ($request->filled('status') || $request->filled('call_status')) {
            $status = $request->input('status') ?? $request->input('call_status');
            if ($status !== 'all') {
                $query->where('call_status', $status);
            }
        }

        // Filter by search query
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('phone', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_code', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        // Date range filters
        $date = $request->input('date');
        $month = $request->input('month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!empty($startDate) && !empty($endDate)) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->orWhereBetween('call_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });
        } elseif (!empty($date)) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('created_at', $date)
                    ->orWhereDate('call_start_time', $date);
            });
        } elseif (!empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->where(function ($q) use ($year, $selectedMonth) {
                $q->whereYear('created_at', $year ?: date('Y'))
                    ->whereMonth('created_at', $selectedMonth ?: date('m'));
            });
        }

        $perPage = max(1, min((int) ($request->input('per_page', 15)), 100));
        $paginated = $query->orderBy('call_id', 'desc')->paginate($perPage);

        // Transform items to match the exact mobile parameters
        $paginated->getCollection()->transform(function ($item) {
            return $this->formatCallLogItem($item);
        });

        return response()->json([
            'status' => true,
            'data' => $paginated,
        ]);
    }

    /**
     * Store a call log / call recording with mobile parameters.
     * POST /api/v1/call-logs (and POST /api/v1/call-recordings)
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'lead_id' => ['nullable'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'call_start_time' => ['nullable'],
            'call_end_time' => ['nullable'],
            'call_duration' => ['nullable', 'string', 'max:50'],
            'duration' => ['nullable', 'string', 'max:50'],
            'customer_id' => ['nullable'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'followup_id' => ['nullable'],
            'followup_date' => ['nullable'],
            'followup_date' => ['nullable'],
            'status' => ['nullable', 'string', 'max:100'],
            'call_status' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'call_type' => ['nullable', 'string', 'max:50'],
            'recording_file' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneNumber = trim((string) ($request->input('phone_number') ?? $request->input('phone')));
        if (empty($phoneNumber)) {
            return response()->json([
                'status' => false,
                'message' => 'Phone number is required.',
                'errors' => ['phone_number' => ['The phone number field is required.']]
            ], 422);
        }

        // Parse Start & End Times
        $startTime = null;
        if ($request->filled('call_start_time')) {
            try {
                $startTime = Carbon::parse($request->input('call_start_time'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
            }
        }

        $endTime = null;
        if ($request->filled('call_end_time')) {
            try {
                $endTime = Carbon::parse($request->input('call_end_time'))->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
            }
        }

        // Duration calculation or assignment
        $duration = $request->input('call_duration') ?? $request->input('duration');
        if (empty($duration) && $startTime && $endTime) {
            try {
                $s = Carbon::parse($startTime);
                $e = Carbon::parse($endTime);
                $diffSec = max(0, $s->diffInSeconds($e));
                $duration = gmdate('i:s', $diffSec);
            } catch (\Exception $e) {
            }
        }

        $leadId = null;
        $customerId = null;
        $customerCode = null;
        $customerName = trim((string) $request->input('customer_name'));
        $followupId = null;
        $followupCode = null;
        $followupDate = null;

        // 1. Parse Followup ID & Followup Code (if calling from Follow-ups Page)
        $rawFollowupId = $request->input('followup_id');
        $followupCode = !empty($rawFollowupId) ? (string) $rawFollowupId : null;

        if (!empty($rawFollowupId)) {
            $fDigits = preg_replace('/\D/', '', (string) $rawFollowupId);
            if (!empty($fDigits)) {
                $foundFoll = Followup::with('lead.customer')->find((int) $fDigits);
                if ($foundFoll) {
                    $followupId = $foundFoll->followups_id;
                    if (empty($leadId) && !empty($foundFoll->lead_id)) {
                        $leadId = $foundFoll->lead_id;
                    }
                    if (empty($customerId) && !empty($foundFoll->lead?->customer_id)) {
                        $customerId = $foundFoll->lead->customer_id;
                        $customerCode = "CUST_{$customerId}";
                    }
                    if (empty($customerName) && !empty($foundFoll->lead?->customer?->name)) {
                        $customerName = $foundFoll->lead->customer->name;
                    }
                    if (empty($followupDate) && !empty($foundFoll->next_followup_date)) {
                        $followupDate = Carbon::parse($foundFoll->next_followup_date)->format('Y-m-d');
                    }
                }
            }
        }

        // 2. Parse Lead ID & Lead Code (if calling from Leads Page)
        $rawLeadId = $request->input('lead_id');
        if (!empty($rawLeadId) && empty($leadId)) {
            $lDigits = preg_replace('/\D/', '', (string) $rawLeadId);
            if (!empty($lDigits)) {
                $foundLead = Lead::with('customer')->find((int) $lDigits);
                if ($foundLead) {
                    $leadId = $foundLead->lead_id;
                    if (empty($customerId) && !empty($foundLead->customer_id)) {
                        $customerId = $foundLead->customer_id;
                        $customerCode = "CUST_{$foundLead->customer_id}";
                    }
                    if (empty($customerName) && !empty($foundLead->customer?->name)) {
                        $customerName = $foundLead->customer->name;
                    }
                } else {
                    $leadId = (int) $lDigits;
                }
            }
        }

        // 3. Parse Customer ID & Customer Code (if calling from Customers Page)
        $rawCustomerId = $request->input('customer_id');
        if (!empty($rawCustomerId)) {
            $digitsOnly = preg_replace('/\D/', '', (string) $rawCustomerId);
            if (!empty($digitsOnly)) {
                $foundCust = Customer::find((int) $digitsOnly);
                if ($foundCust) {
                    $customerId = $foundCust->customer_id;
                    $customerCode = "CUST_{$foundCust->customer_id}";
                    if (empty($customerName)) {
                        $customerName = $foundCust->name;
                    }
                }
            }
        }

        // Fallback: If customer not resolved by ID, attempt lookup by phone number (checking both mobile & alternate_mobile)
        if (!$customerId && !empty($phoneNumber)) {
            $cleanPhone = preg_replace('/[^\d+]/', '', $phoneNumber);
            $last10 = substr(preg_replace('/\D/', '', $cleanPhone), -10);
            if (!empty($last10)) {
                $foundByPhone = Customer::where(function ($q) use ($last10) {
                    $q->where('mobile', 'LIKE', "%{$last10}%")
                      ->orWhere('alternate_mobile', 'LIKE', "%{$last10}%");
                })->first();
                if ($foundByPhone) {
                    $customerId = $foundByPhone->customer_id;
                    if (empty($customerCode)) {
                        $customerCode = "CUST_{$foundByPhone->customer_id}";
                    }
                    if (empty($customerName)) {
                        $customerName = $foundByPhone->name;
                    }
                }
            }
        }

        // 4. Auto-resolve Lead ID from customer_id if lead_id was not explicitly sent
        if (empty($leadId) && !empty($customerId)) {
            $customerLead = Lead::where('customer_id', $customerId)
                ->whereHas('followups')
                ->orderBy('lead_id', 'desc')
                ->first()
                ?? Lead::where('customer_id', $customerId)
                ->orderBy('lead_id', 'desc')
                ->first();
            if ($customerLead) {
                $leadId = $customerLead->lead_id;
            }
        }

        // 4b. Fallback: If leadId is still empty, check if customer_name or phone_number matches a customer who has leads
        if (empty($leadId)) {
            if (!empty($customerName)) {
                $matchedByName = Customer::where('name', 'LIKE', "%{$customerName}%")
                    ->whereHas('leads')
                    ->first();
                if ($matchedByName) {
                    $customerLead = Lead::where('customer_id', $matchedByName->customer_id)
                        ->whereHas('followups')
                        ->orderBy('lead_id', 'desc')
                        ->first()
                        ?? Lead::where('customer_id', $matchedByName->customer_id)
                        ->orderBy('lead_id', 'desc')
                        ->first();
                    if ($customerLead) {
                        $leadId = $customerLead->lead_id;
                        $customerId = $matchedByName->customer_id;
                        $customerCode = "CUST_{$matchedByName->customer_id}";
                        $customerName = $matchedByName->name;
                    }
                }
            }

            if (empty($leadId) && !empty($phoneNumber)) {
                $cleanPhone = preg_replace('/[^\d+]/', '', $phoneNumber);
                $last10 = substr(preg_replace('/\D/', '', $cleanPhone), -10);
                if (!empty($last10)) {
                    $matchedByPhone = Customer::where(function ($q) use ($last10) {
                        $q->where('mobile', 'LIKE', "%{$last10}%")
                          ->orWhere('alternate_mobile', 'LIKE', "%{$last10}%");
                    })->whereHas('leads')->first();
                    if ($matchedByPhone) {
                        $customerLead = Lead::where('customer_id', $matchedByPhone->customer_id)
                            ->whereHas('followups')
                            ->orderBy('lead_id', 'desc')
                            ->first()
                            ?? Lead::where('customer_id', $matchedByPhone->customer_id)
                            ->orderBy('lead_id', 'desc')
                            ->first();
                        if ($customerLead) {
                            $leadId = $customerLead->lead_id;
                            $customerId = $matchedByPhone->customer_id;
                            $customerCode = "CUST_{$matchedByPhone->customer_id}";
                            if (empty($customerName)) {
                                $customerName = $matchedByPhone->name;
                            }
                        }
                    }
                }
            }
        }

        // 5. Auto-resolve Followup ID from lead_id if followup_id was not explicitly sent
        if (empty($followupId) && !empty($leadId)) {
            $leadFollowup = Followup::where('lead_id', $leadId)
                ->orderBy('followups_id', 'desc')
                ->first();
            if ($leadFollowup) {
                $followupId = $leadFollowup->followups_id;
                $followupCode = "FOL_{$followupId}";
                if (empty($followupDate) && !empty($leadFollowup->next_followup_date)) {
                    $followupDate = Carbon::parse($leadFollowup->next_followup_date)->format('Y-m-d');
                }
            }
        }

        // Parse Followup Date (handles "25-09-2026", "2026-09-25", etc.)
        $rawFollowupDate = $request->input('followuo_date') ?? $request->input('followup_date');
        if (!empty($rawFollowupDate)) {
            try {
                $followupDate = Carbon::parse($rawFollowupDate)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        // Detect or set call source (customers / leads / followups / direct)
        $callSource = $request->input('call_source');
        if (empty($callSource)) {
            if (!empty($followupId)) {
                $callSource = 'followup';
            } elseif (!empty($leadId)) {
                $callSource = 'lead';
            } elseif (!empty($customerId)) {
                $callSource = 'customer';
            } else {
                $callSource = 'direct';
            }
        }

        // Status
        $status = $request->input('status') ?? $request->input('call_status') ?? 'Answered';
        $callType = $request->input('call_type') ?? 'Outbound';
        $notes = $request->input('notes');

        // Handle Audio Recording file upload or file path string
        $recordingFilePath = null;
        $recordingId = null;

        if ($request->hasFile('recording_file')) {
            $file = $request->file('recording_file');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/call_recordings');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $recordingFilePath = 'uploads/call_recordings/' . $fileName;

            $recording = CallRecording::create([
                'lead_id' => $leadId,
                'customer_id' => $customerId,
                'recording_file' => $recordingFilePath,
                'duration' => $duration,
                'created_by' => $currentUser->id,
            ]);
            $recordingId = $recording->call_id;
        } elseif ($request->filled('recording_file') && is_string($request->input('recording_file'))) {
            $rawFileString = trim($request->input('recording_file'));
            $recordingFilePath = $rawFileString;

            $recording = CallRecording::create([
                'lead_id' => $leadId,
                'customer_id' => $customerId,
                'recording_file' => $recordingFilePath,
                'duration' => $duration,
                'created_by' => $currentUser->id,
            ]);
            $recordingId = $recording->call_id;
        }

        // Create Call Log record
        $callLog = CallLog::create([
            'user_id' => $currentUser->id,
            'lead_id' => $leadId,
            'customer_id' => $customerId,
            'customer_code' => $customerCode ?? (!empty($customerId) ? "CUST_{$customerId}" : null),
            'customer_name' => !empty($customerName) ? $customerName : null,
            'followup_id' => $followupId,
            'followup_code' => $followupCode ?? (!empty($followupId) ? "FOL_{$followupId}" : null),
            'followup_date' => $followupDate,
            'phone' => $phoneNumber,
            'call_type' => $callType,
            'call_source' => $callSource,
            'duration' => $duration,
            'call_start_time' => $startTime,
            'call_end_time' => $endTime,
            'call_status' => $status,
            'notes' => $notes,
            'recording_id' => $recordingId,
            'recording_file' => $recordingFilePath,
            'created_at' => $startTime ? Carbon::parse($startTime) : Carbon::now(),
        ]);

        $formattedData = $this->formatCallLogItem($callLog->fresh(['customer', 'followup', 'recording', 'user']));

        return response()->json([
            'status' => true,
            'message' => 'Call log recorded successfully.',
            'data' => $formattedData,
        ], 201);
    }

    /**
     * Get single call log details.
     * GET /api/v1/call-logs/{id}
     */
    public function show($id, Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $log = CallLog::forUser($currentUser)
            ->with(['customer', 'followup', 'lead', 'user', 'recording'])
            ->find($id);

        if (!$log) {
            return response()->json([
                'status' => false,
                'message' => 'Call log record not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $this->formatCallLogItem($log),
        ]);
    }

    /**
     * Helper to transform CallLog model into the exact requested mobile format.
     */
    private function formatCallLogItem(CallLog $log): array
    {
        // Format timestamps as ISO string if available
        $startTimeIso = $log->call_start_time ? $log->call_start_time->toISOString() : null;
        $endTimeIso = $log->call_end_time ? $log->call_end_time->toISOString() : null;

        // Customer identifier
        $customerIdStr = $log->customer_code
            ?? (!empty($log->customer_id) ? "CUST_{$log->customer_id}" : null);

        $customerNameStr = $log->customer_name
            ?? ($log->customer ? $log->customer->name : null);

        // Follow-up identifier & date
        $followupIdStr = $log->followup_code
            ?? (!empty($log->followup_id) ? "FOL_{$log->followup_id}" : null);

        $followupDateFormatted = $log->followup_date
            ? Carbon::parse($log->followup_date)->format('d-m-Y')
            : null;

        // Recording file and URL
        $recordingFile = $log->recording_file
            ?? ($log->recording ? $log->recording->recording_file : null);

        $recordingUrl = $log->recording_url;
        if (empty($recordingUrl) && !empty($recordingFile)) {
            $recordingUrl = filter_var($recordingFile, FILTER_VALIDATE_URL) ? $recordingFile : asset($recordingFile);
        }

        return [
            'call_id' => (int) $log->call_id,
            'lead_id' => $log->lead_id ? (int) $log->lead_id : null,
            'lead_title' => $log->lead ? $log->lead->lead_title : null,
            'phone_number' => $log->phone,
            'call_start_time' => $startTimeIso,
            'call_end_time' => $endTimeIso,
            'call_duration' => $log->duration,
            'customer_id' => $customerIdStr,
            'customer_name' => $customerNameStr,
            'followup_id' => $followupIdStr,
            'followuo_date' => $followupDateFormatted,
            'followup_date' => $followupDateFormatted,
            'status' => $log->call_status,
            'notes' => $log->notes,
            'recording_file' => $recordingFile ? basename($recordingFile) : null,
            'recording_path' => $recordingFile,
            'recording_url' => $recordingUrl,
            'call_type' => $log->call_type,
            'call_source' => $log->call_source ?? 'direct',
            'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : null,
            'staff_id' => $log->user_id,
            'staff_name' => $log->user ? $log->user->name : null,
        ];
    }
}
