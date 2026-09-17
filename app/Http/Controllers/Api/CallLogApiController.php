<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\CallRecording;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
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
        // Date range filters (support start_date/end_date, from_date/to_date, date, or month)
        $date = $request->input('date');
        $month = $request->input('month');
        $startDate = $request->input('start_date') ?? $request->input('from_date');
        $endDate = $request->input('end_date') ?? $request->input('to_date');

        if (!empty($startDate) && !empty($endDate)) {
            $query->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('call_start_time', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                    ->orWhereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            });
        } elseif (!empty($date)) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('call_start_time', $date)
                    ->orWhereDate('created_at', $date);
            });
        } elseif (!empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $y = $year ?: date('Y');
            $m = $selectedMonth ?: date('m');
            $query->where(function ($q) use ($y, $m) {
                $q->where(function ($sub) use ($y, $m) {
                    $sub->whereYear('call_start_time', $y)->whereMonth('call_start_time', $m);
                })->orWhere(function ($sub) use ($y, $m) {
                    $sub->whereYear('created_at', $y)->whereMonth('created_at', $m);
                });
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
            'followuo_date' => ['nullable'],
            'next_followup_date' => ['nullable'],
            'followup_type' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string'],
            'forward_to' => ['nullable'],
            'followup_status' => ['nullable', 'string', 'max:50'],
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
        $tz = config('app.timezone', 'Asia/Kolkata');
        $startTime = null;
        if ($request->filled('call_start_time')) {
            try {
                $startTime = Carbon::parse($request->input('call_start_time'))->setTimezone($tz)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
            }
        }

        $endTime = null;
        if ($request->filled('call_end_time')) {
            try {
                $endTime = Carbon::parse($request->input('call_end_time'))->setTimezone($tz)->format('Y-m-d H:i:s');
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

        // Status & Notes
        $status = $request->input('status') ?? $request->input('call_status') ?? 'Answered';
        $callType = $request->input('call_type') ?? 'Outbound';
        $notes = $request->input('notes');

        // 6. Handle Followup Date & store in followups table
        // Note: ONLY if followup_date is posted in the request, insert a record into the followups table.
        $rawFollowupDate = $request->input('followup_date')
            ?? $request->input('followuo_date')
            ?? $request->input('next_followup_date');

        if (!empty($rawFollowupDate)) {
            $tz = config('app.timezone', 'Asia/Kolkata');
            $parsedFollowupDateTime = null;
            try {
                $parsedFollowupDateTime = Carbon::parse($rawFollowupDate)->setTimezone($tz)->format('Y-m-d H:i:s');
                $followupDate = Carbon::parse($rawFollowupDate)->setTimezone($tz)->format('Y-m-d');
            } catch (\Exception $e) {
                try {
                    $parsedFollowupDateTime = Carbon::createFromFormat('d-m-Y', $rawFollowupDate)->setTimezone($tz)->format('Y-m-d 00:00:00');
                    $followupDate = Carbon::createFromFormat('d-m-Y', $rawFollowupDate)->setTimezone($tz)->format('Y-m-d');
                } catch (\Exception $e2) {
                }
            }

            if (!empty($parsedFollowupDateTime)) {
                // Ensure a valid lead exists (required for followups foreign key)
                if (empty($leadId)) {
                    if (!empty($customerId)) {
                        $custLead = Lead::where('customer_id', $customerId)->orderBy('lead_id', 'desc')->first();
                        if ($custLead) {
                            $leadId = $custLead->lead_id;
                        } else {
                            $newLead = Lead::create([
                                'customer_id'        => $customerId,
                                'lead_title'         => 'Call Inquiry - ' . (!empty($customerName) ? $customerName : $phoneNumber),
                                'assigned_to'        => $currentUser->id,
                                'created_by'         => $currentUser->id,
                                'status'             => 'Active',
                                'next_followup_date' => $parsedFollowupDateTime,
                            ]);
                            $leadId = $newLead->lead_id;
                        }
                    } else {
                        // Create customer and lead
                        $newCust = Customer::create([
                            'name'       => !empty($customerName) ? $customerName : "Customer ({$phoneNumber})",
                            'mobile'     => $phoneNumber,
                            'created_by' => $currentUser->id,
                        ]);
                        $customerId = $newCust->customer_id;
                        $customerCode = "CUST_{$customerId}";
                        $customerName = $newCust->name;

                        $newLead = Lead::create([
                            'customer_id'        => $customerId,
                            'lead_title'         => 'Call Inquiry - ' . $customerName,
                            'assigned_to'        => $currentUser->id,
                            'created_by'         => $currentUser->id,
                            'status'             => 'Active',
                            'next_followup_date' => $parsedFollowupDateTime,
                        ]);
                        $leadId = $newLead->lead_id;
                    }
                }

                // If user called from an existing pending followup, mark it Completed
                if (!empty($followupId)) {
                    $prevFollowup = Followup::find($followupId);
                    if ($prevFollowup && in_array(strtolower($prevFollowup->followup_status ?? ''), ['pending', 'open'])) {
                        $prevFollowup->update(['followup_status' => 'Completed']);
                    }
                }

                // Insert record into followups table
                $followupType = $request->input('followup_type') ?? 'Call';
                $followupRemarks = $request->input('remarks') ?? $notes ?? null;
                $forwardTo = $request->input('forward_to')
                    ?? (!empty($leadId) ? Lead::where('lead_id', $leadId)->value('assigned_to') : null)
                    ?? $currentUser->id;
                $newFollowupStatus = $request->input('followup_status') ?? 'Pending';

                $newFollowup = Followup::create([
                    'lead_id'            => (int) $leadId,
                    'followup_type'      => $followupType,
                    'duration'           => $duration,
                    'remarks'            => $followupRemarks,
                    'next_followup_date' => $parsedFollowupDateTime,
                    'followup_status'    => $newFollowupStatus,
                    'forward_to'         => $forwardTo,
                    'created_by'         => $currentUser->id,
                ]);

                // Sync next_followup_date to the Lead model
                if (!empty($leadId)) {
                    Lead::where('lead_id', $leadId)->update([
                        'next_followup_date' => $parsedFollowupDateTime,
                    ]);
                }

                // Link this newly created followup to the call log
                $followupId = $newFollowup->followups_id;
                $followupCode = "FOL_{$newFollowup->followups_id}";
            }
        }

        // Detect or set call source (customers / leads / followups / direct)
        $callSource = $request->input('call_source');
        if (empty($callSource)) {
            if (!empty($rawFollowupId)) {
                $callSource = 'followup';
            } elseif (!empty($rawLeadId)) {
                $callSource = 'lead';
            } elseif (!empty($rawCustomerId)) {
                $callSource = 'customer';
            } else {
                $callSource = 'direct';
            }
        }

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
        $tz = config('app.timezone', 'Asia/Kolkata');

        // Format timestamps in local application timezone (no UTC Z shift) so mobile parses as local time
        $startTime = $log->call_start_time ? $log->call_start_time->copy()->setTimezone($tz) : null;
        $endTime = $log->call_end_time ? $log->call_end_time->copy()->setTimezone($tz) : null;
        $createdAt = $log->created_at ? $log->created_at->copy()->setTimezone($tz) : null;

        $primaryTime = $startTime ?: $createdAt;

        $startTimeFormatted = $startTime ? $startTime->format('Y-m-d\TH:i:s') : null;
        $endTimeFormatted = $endTime ? $endTime->format('Y-m-d\TH:i:s') : null;

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

        $recordingUrl = $log->recording_url ?: get_media_url($recordingFile);

        return [
            'call_id' => (int) $log->call_id,
            'lead_id' => $log->lead_id ? (int) $log->lead_id : null,
            'lead_title' => $log->lead ? $log->lead->lead_title : null,
            'phone_number' => $log->phone,
            'call_start_time' => $startTimeFormatted,
            'call_end_time' => $endTimeFormatted,
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
            'created_at' => $createdAt ? $createdAt->format('Y-m-d H:i:s') : null,
            'call_time' => $primaryTime ? $primaryTime->format('g:i A') : '',
            'call_date' => $primaryTime ? $primaryTime->format('d M Y') : '',
            'call_date_time' => $primaryTime ? $primaryTime->format('d M Y, h:i A') : '',
            'staff_id' => $log->user_id,
            'staff_name' => $log->user ? $log->user->name : null,
        ];
    }

    /**
     * Mobile App Call Report API with date-wise filtering, time-based breakdown & summary.
     * GET /api/v1/call-report
     */
    public function report(Request $request): JsonResponse
    {
        $currentUser = $request->user() ?? Auth::user() ?? auth('sanctum')->user();
        if (!$currentUser) {
            return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
        }

        // Parse date range (supports from_date/to_date, start_date/end_date, or single date)
        $rawFromDate = $request->input('from_date') ?? $request->input('start_date') ?? $request->input('date') ?? date('Y-m-d');
        $rawToDate = $request->input('to_date') ?? $request->input('end_date') ?? $request->input('date') ?? date('Y-m-d');

        try {
            $fromDate = Carbon::parse($rawFromDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $fromDate = date('Y-m-d');
        }

        try {
            $toDate = Carbon::parse($rawToDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $toDate = date('Y-m-d');
        }

        if ($fromDate > $toDate) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        // Base query for user's calls in the selected date range (checks call_start_time or created_at)
        $baseQuery = CallLog::forUser($currentUser)
            ->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('call_start_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                    ->orWhereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            });

        if ($request->filled('staff_id') || $request->filled('user_id')) {
            $targetStaffId = $request->input('staff_id') ?? $request->input('user_id');
            if ($currentUser->isAdmin()) {
                $baseQuery->where('user_id', $targetStaffId);
            }
        }

        $tz = config('app.timezone', 'Asia/Kolkata');
        $allDateCalls = (clone $baseQuery)->orderBy('call_id', 'ASC')->get();

        // 1. Top Card Header
        $isSingleDay = ($fromDate === $toDate);
        $isToday = ($fromDate === date('Y-m-d') && $isSingleDay);
        $reportTitle = $isToday ? "Today's Call Report" : "Call Report";
        $formattedDate = $isSingleDay
            ? Carbon::parse($fromDate)->format('d M Y')
            : Carbon::parse($fromDate)->format('d M Y') . ' – ' . Carbon::parse($toDate)->format('d M Y');

        // Earliest and latest call time in application timezone
        $firstCall = $allDateCalls->first();
        $lastCall = $allDateCalls->last();
        $firstTime = $firstCall ? ($firstCall->call_start_time ?: $firstCall->created_at)?->copy()->setTimezone($tz) : null;
        $lastTime = $lastCall ? ($lastCall->call_start_time ?: $lastCall->created_at)?->copy()->setTimezone($tz) : null;
        $timeRangeText = ($firstTime && $lastTime)
            ? $firstTime->format('g:i A') . ' – ' . $lastTime->format('g:i A')
            : '09:00 AM – 06:00 PM';

        // 2. Summary counts
        $totalCustomers = Customer::forUser($currentUser)->count();
        if ($totalCustomers === 0) {
            $totalCustomers = Customer::count();
        }

        // Calls completed (answered / completed calls)
        $answeredCallsCount = $allDateCalls->filter(fn($c) => in_array(strtolower(trim($c->call_status ?? '')), ['answered', 'completed']))->count();
        $busyCallsCount = $allDateCalls->filter(fn($c) => in_array(strtolower(trim($c->call_status ?? '')), ['busy', 'line busy']))->count();
        $noAnswerCallsCount = $allDateCalls->filter(fn($c) => in_array(strtolower(trim($c->call_status ?? '')), ['no answer', 'missed', 'rejected', 'declined', 'unanswered']))->count();
        $switchedOffCallsCount = $allDateCalls->filter(fn($c) => in_array(strtolower(trim($c->call_status ?? '')), ['switched off', 'switch off', 'switched_off', 'not reachable', 'out of reach']))->count();

        // Calls completed in top card
        $callsCompleted = $answeredCallsCount;
        $pendingCalls = max(0, $totalCustomers - $callsCompleted);

        // 3. Time-based Breakdown (based on actual call time in application timezone)
        $slot1Count = $allDateCalls->filter(function ($c) use ($tz) {
            $timeObj = ($c->call_start_time ?: $c->created_at)?->copy()->setTimezone($tz);
            $t = $timeObj ? $timeObj->format('H:i') : '00:00';
            return $t >= '09:00' && $t < '11:30';
        })->count();

        $fullShiftCount = $allDateCalls->filter(function ($c) use ($tz) {
            $timeObj = ($c->call_start_time ?: $c->created_at)?->copy()->setTimezone($tz);
            $t = $timeObj ? $timeObj->format('H:i') : '00:00';
            return $t >= '09:00' && $t <= '18:00';
        })->count();

        $timeBreakdown = [
            [
                'slot' => '9:00 AM → 11:30 AM',
                'label' => 'Morning Session',
                'calls_count' => $slot1Count,
                'calls_text' => "{$slot1Count} calls",
            ],
            [
                'slot' => '9:00 AM → 6:00 PM',
                'label' => 'Full Day Shift',
                'calls_count' => $fullShiftCount,
                'calls_text' => "{$fullShiftCount} calls",
            ],
        ];

        // 4. Filter Call Details list by selected status pill
        $statusFilter = trim((string) ($request->input('status') ?? $request->input('call_status') ?? 'All'));
        if ($statusFilter === '') {
            $statusFilter = 'All';
        }
        $filteredQuery = clone $baseQuery;

        if (!empty($statusFilter) && strtolower($statusFilter) !== 'all') {
            $sf = strtolower($statusFilter);
            if ($sf === 'answered') {
                $filteredQuery->where(function ($q) {
                    $q->where('call_status', 'Answered')
                      ->orWhere('call_status', 'Completed');
                });
            } elseif ($sf === 'busy') {
                $filteredQuery->where(function ($q) {
                    $q->where('call_status', 'Busy')
                      ->orWhere('call_status', 'Line Busy');
                });
            } elseif (in_array($sf, ['switched off', 'switch off', 'switched_off', 'not reachable'])) {
                $filteredQuery->where(function ($q) {
                    $q->where('call_status', 'Switched Off')
                      ->orWhere('call_status', 'Switch Off')
                      ->orWhere('call_status', 'switched_off')
                      ->orWhere('call_status', 'Not Reachable')
                      ->orWhere('call_status', 'Out of Reach');
                });
            } elseif (in_array($sf, ['no answer', 'missed', 'rejected', 'declined'])) {
                $filteredQuery->where(function ($q) {
                    $q->whereIn('call_status', ['No Answer', 'Missed', 'Rejected', 'Declined', 'Unanswered']);
                });
            } else {
                $filteredQuery->where(function ($q) use ($statusFilter) {
                    $q->where('call_status', $statusFilter)
                      ->orWhere('call_status', ucfirst($statusFilter));
                });
            }
        }

        // Call type filter
        if ($request->filled('call_type') && strtolower($request->input('call_type')) !== 'all') {
            $filteredQuery->where('call_type', $request->input('call_type'));
        }

        // Search filter
        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $filteredQuery->where(function ($q) use ($s) {
                $q->where('phone', 'LIKE', "%{$s}%")
                  ->orWhere('customer_name', 'LIKE', "%{$s}%")
                  ->orWhere('customer_code', 'LIKE', "%{$s}%");
            });
        }

        $callLogs = $filteredQuery
            ->with(['customer', 'lead', 'recording', 'user'])
            ->orderBy('call_id', 'DESC')
            ->get();

        // Format Call Details items for mobile UI cards
        $callDetails = $callLogs->map(function ($log) {
            $statusLower = strtolower(trim($log->call_status ?? ''));
            $isAnswered = in_array($statusLower, ['answered', 'completed']);
            $isBusy = in_array($statusLower, ['busy', 'line busy']);
            $isSwitchedOff = in_array($statusLower, ['switched off', 'switch off', 'switched_off', 'not reachable', 'out of reach']);
            $isNoAnswer = in_array($statusLower, ['no answer', 'missed', 'rejected', 'declined', 'unanswered']);

            if ($isAnswered) {
                $displayStatus = 'Answered';
                $statusColor = 'success';
            } elseif ($isBusy) {
                $displayStatus = 'Busy';
                $statusColor = 'warning';
            } elseif ($isSwitchedOff) {
                $displayStatus = 'Switched Off';
                $statusColor = 'secondary';
            } elseif ($isNoAnswer) {
                $displayStatus = 'No Answer';
                $statusColor = 'danger';
            } else {
                $displayStatus = $log->call_status ?: 'No Answer';
                $statusColor = 'danger';
            }

            $callType = $log->call_type ?? 'Outbound';
            $isOutbound = strtolower($callType) !== 'inbound';
            $callDirection = $isOutbound ? 'outbound' : 'inbound';

            $recordingFile = $log->recording_file
                ?? ($log->recording ? $log->recording->recording_file : null);
            $recordingUrl = $log->recording_url ?: get_media_url($recordingFile);

            $tz = config('app.timezone', 'Asia/Kolkata');
            $startTime = $log->call_start_time ? $log->call_start_time->copy()->setTimezone($tz) : null;
            $createdAt = $log->created_at ? $log->created_at->copy()->setTimezone($tz) : null;
            $primaryTime = $startTime ?: $createdAt;

            return [
                'call_id' => (int) $log->call_id,
                'customer_name' => $log->customer_name ?: ($log->customer ? $log->customer->name : 'Unknown Customer'),
                'customer_id' => $log->customer_code ?? (!empty($log->customer_id) ? "CUST_{$log->customer_id}" : null),
                'phone_number' => $log->phone,
                'call_time' => $primaryTime ? $primaryTime->format('g:i A') : '',
                'call_date' => $primaryTime ? $primaryTime->format('d M Y') : '',
                'call_start_time' => $startTime ? $startTime->format('Y-m-d\TH:i:s') : null,
                'status' => $displayStatus,
                'status_color' => $statusColor,
                'call_type' => $callType,
                'call_direction' => $callDirection,
                'duration' => $log->duration ?? '00:00',
                'lead_id' => $log->lead_id ? (int) $log->lead_id : null,
                'lead_title' => $log->lead ? $log->lead->lead_title : null,
                'notes' => $log->notes,
                'recording_file' => $recordingFile ? basename($recordingFile) : null,
                'recording_url' => $recordingUrl,
            ];
        })->values();



        return response()->json([
            'status' => true,
            'message' => 'Call report fetched successfully.',
            'data' => [
                'top_card' => [
                    'report_title' => $reportTitle,
                    'date_text' => $formattedDate,
                    'time_range' => $timeRangeText,
                    'total_customers' => $totalCustomers,
                    'calls_completed' => $callsCompleted,
                    'pending_calls' => $pendingCalls,
                ],
                'time_breakdown' => $timeBreakdown,
                'filter_options' => [
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'status' => $statusFilter,
                    'active_status' => $statusFilter,
                    'call_status' => $statusFilter,
                    'status_pills' => ['All', 'Answered', 'Busy', 'No Answer', 'Switched Off'],
                    'status_options' => [
                        [
                            'key' => 'All',
                            'label' => 'All',
                            'count' => $allDateCalls->count(),
                            'is_selected' => strtolower($statusFilter) === 'all' || empty($statusFilter),
                        ],
                        [
                            'key' => 'Answered',
                            'label' => 'Answered',
                            'count' => $answeredCallsCount,
                            'is_selected' => strtolower($statusFilter) === 'answered',
                        ],
                        [
                            'key' => 'Busy',
                            'label' => 'Busy',
                            'count' => $busyCallsCount,
                            'is_selected' => strtolower($statusFilter) === 'busy',
                        ],
                        [
                            'key' => 'No Answer',
                            'label' => 'No Answer',
                            'count' => $noAnswerCallsCount,
                            'is_selected' => in_array(strtolower($statusFilter), ['no answer', 'missed', 'rejected', 'declined']),
                        ],
                        [
                            'key' => 'Switched Off',
                            'label' => 'Switched Off',
                            'count' => $switchedOffCallsCount,
                            'is_selected' => in_array(strtolower($statusFilter), ['switched off', 'switch off', 'switched_off', 'not reachable']),
                        ],
                    ],
                ],
                'calls_count' => $callDetails->count(),
                'call_details' => $callDetails,
            ],
        ]);
    }

    /**
     * Generate and export Call Report as PDF.
     * Supports both:
     * 1. Without filter: generates report for all available call records.
     * 2. With filter: generates report applying date, status, staff, type, search filters.
     *
     * GET /api/v1/call-report/pdf
     * GET /api/v1/call-report-pdf
     */
    public function exportPdf(Request $request)
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

        $query = CallLog::forUser($currentUser);

        // Check if any date filter is applied
        $hasDateFilter = $request->filled('date')
            || $request->filled('from_date')
            || $request->filled('to_date')
            || $request->filled('start_date')
            || $request->filled('end_date');

        $isFiltered = false;

        if ($hasDateFilter) {
            $isFiltered = true;
            if ($request->filled('date')) {
                try {
                    $fromDate = Carbon::parse($request->input('date'))->format('Y-m-d');
                } catch (\Exception $e) {
                    $fromDate = date('Y-m-d');
                }
                $toDate = $fromDate;
            } else {
                $rawFromDate = $request->input('from_date') ?? $request->input('start_date') ?? date('Y-m-d');
                $rawToDate = $request->input('to_date') ?? $request->input('end_date') ?? date('Y-m-d');

                try {
                    $fromDate = Carbon::parse($rawFromDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $fromDate = date('Y-m-d');
                }

                try {
                    $toDate = Carbon::parse($rawToDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $toDate = date('Y-m-d');
                }

                if ($fromDate > $toDate) {
                    [$fromDate, $toDate] = [$toDate, $fromDate];
                }
            }

            $dateRangeText = ($fromDate === $toDate)
                ? Carbon::parse($fromDate)->format('d M Y')
                : Carbon::parse($fromDate)->format('d M Y') . ' - ' . Carbon::parse($toDate)->format('d M Y');

            $query->where(function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('call_start_time', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                  ->orWhereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59']);
            });
        } else {
            // Scenario 1: Without filter - show all call logs
            $dateRangeText = 'All Time';
        }

        // Status Filter
        $statusFilter = $request->input('status');
        if ($request->filled('status') && strtolower($statusFilter) !== 'all') {
            $isFiltered = true;
            $sf = strtolower($statusFilter);
            if ($sf === 'answered') {
                $query->where(function ($q) {
                    $q->where('call_status', 'Answered')
                      ->orWhere('call_status', 'Completed');
                });
            } elseif ($sf === 'busy') {
                $query->where(function ($q) {
                    $q->where('call_status', 'Busy')
                      ->orWhere('call_status', 'Line Busy');
                });
            } elseif (in_array($sf, ['switched off', 'switch off', 'switched_off', 'not reachable'])) {
                $query->where(function ($q) {
                    $q->where('call_status', 'Switched Off')
                      ->orWhere('call_status', 'Switch Off')
                      ->orWhere('call_status', 'switched_off')
                      ->orWhere('call_status', 'Not Reachable')
                      ->orWhere('call_status', 'Out of Reach');
                });
            } elseif (in_array($sf, ['no answer', 'missed', 'rejected', 'declined'])) {
                $query->where(function ($q) {
                    $q->whereIn('call_status', ['No Answer', 'Missed', 'Rejected', 'Declined', 'Unanswered']);
                });
            } else {
                $query->where(function ($q) use ($statusFilter) {
                    $q->where('call_status', $statusFilter)
                      ->orWhere('call_status', ucfirst($statusFilter));
                });
            }
        }

        // Staff / User filter
        $staffName = null;
        if ($request->filled('staff_id') || $request->filled('user_id')) {
            $isFiltered = true;
            $targetStaffId = $request->input('staff_id') ?? $request->input('user_id');
            if ($currentUser->isAdmin()) {
                $query->where('user_id', $targetStaffId);
            }
            $staffUser = User::find($targetStaffId);
            if ($staffUser) {
                $staffName = $staffUser->name;
            }
        }

        // Call type filter (Inbound / Outbound)
        if ($request->filled('call_type') && strtolower($request->input('call_type')) !== 'all') {
            $isFiltered = true;
            $query->where('call_type', $request->input('call_type'));
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $isFiltered = true;
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Lead filter
        if ($request->filled('lead_id')) {
            $isFiltered = true;
            $query->where('lead_id', $request->input('lead_id'));
        }

        // Phone search filter
        if ($request->filled('phone')) {
            $isFiltered = true;
            $query->where('phone', 'LIKE', '%' . trim($request->input('phone')) . '%');
        }

        // Keyword search
        if ($request->filled('search')) {
            $isFiltered = true;
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('phone', 'LIKE', "%{$s}%")
                  ->orWhere('customer_name', 'LIKE', "%{$s}%")
                  ->orWhere('customer_code', 'LIKE', "%{$s}%");
            });
        }

        $filterMode = $isFiltered ? 'Filtered' : 'All Records (No Filter)';

        $callLogs = $query
            ->with(['customer', 'lead', 'recording', 'user'])
            ->orderBy('call_id', 'DESC')
            ->get();

        // Calculate summary statistics
        $totalCalls = $callLogs->count();
        $answeredCalls = 0;
        $busyCalls = 0;
        $noAnswerCalls = 0;
        $totalDurationSec = 0;

        $tz = config('app.timezone', 'Asia/Kolkata');
        $formattedCalls = [];

        foreach ($callLogs as $log) {
            $statusLower = strtolower(trim($log->call_status ?? ''));
            if (in_array($statusLower, ['answered', 'completed'])) {
                $answeredCalls++;
                $displayStatus = 'Answered';
            } elseif (in_array($statusLower, ['busy', 'line busy'])) {
                $busyCalls++;
                $displayStatus = 'Busy';
            } elseif (in_array($statusLower, ['switched off', 'switch off', 'switched_off', 'not reachable', 'out of reach'])) {
                $noAnswerCalls++;
                $displayStatus = 'Switched Off';
            } elseif (in_array($statusLower, ['no answer', 'missed', 'rejected', 'declined', 'unanswered'])) {
                $noAnswerCalls++;
                $displayStatus = 'No Answer';
            } else {
                $displayStatus = $log->call_status ?: 'No Answer';
                $noAnswerCalls++;
            }

            // Duration calculation
            $dur = $log->duration;
            $durSec = 0;
            if (is_numeric($dur)) {
                $durSec = (int) $dur;
            } elseif (is_string($dur) && str_contains($dur, ':')) {
                $parts = explode(':', $dur);
                if (count($parts) === 3) {
                    $durSec = ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
                } elseif (count($parts) === 2) {
                    $durSec = ((int)$parts[0] * 60) + (int)$parts[1];
                }
            }
            $totalDurationSec += $durSec;

            $formattedDuration = $durSec > 0
                ? sprintf('%02d:%02d', floor($durSec / 60), $durSec % 60)
                : ($log->duration ?: '00:00');

            $startTime = $log->call_start_time ? $log->call_start_time->copy()->setTimezone($tz) : null;
            $createdAt = $log->created_at ? $log->created_at->copy()->setTimezone($tz) : null;
            $primaryTime = $startTime ?: $createdAt;

            $customerName = $log->customer_name ?: ($log->customer ? $log->customer->name : 'Unknown Customer');
            $customerId = $log->customer_code ?? (!empty($log->customer_id) ? "CUST_{$log->customer_id}" : null);

            $formattedCalls[] = [
                'call_id' => (int) $log->call_id,
                'call_date' => $primaryTime ? $primaryTime->format('d M Y') : '—',
                'call_time' => $primaryTime ? $primaryTime->format('g:i A') : '—',
                'customer_name' => $customerName,
                'customer_id' => $customerId,
                'phone_number' => $log->phone ?: '—',
                'lead_id' => $log->lead_id ? (int) $log->lead_id : null,
                'lead_title' => $log->lead ? $log->lead->lead_title : null,
                'call_type' => $log->call_type ? ucfirst($log->call_type) : 'Outbound',
                'duration' => $formattedDuration,
                'status' => $displayStatus,
                'staff_name' => $log->user ? $log->user->name : 'Staff',
                'notes' => $log->notes,
            ];
        }

        // Format total duration string
        $hours = floor($totalDurationSec / 3600);
        $minutes = floor(($totalDurationSec % 3600) / 60);
        $seconds = $totalDurationSec % 60;
        if ($hours > 0) {
            $formattedTotalDuration = "{$hours}h {$minutes}m {$seconds}s";
        } elseif ($minutes > 0) {
            $formattedTotalDuration = "{$minutes}m {$seconds}s";
        } else {
            $formattedTotalDuration = "{$seconds}s";
        }

        $viewData = [
            'filters' => [
                'date_range_text' => $dateRangeText,
                'status' => $request->input('status', 'All'),
                'staff_name' => $staffName,
                'call_type' => $request->input('call_type', 'All'),
                'filter_mode' => $filterMode,
            ],
            'generated_at' => Carbon::now($tz)->format('d M Y, g:i A'),
            'generated_by' => $currentUser->name ?? 'Admin',
            'summary' => [
                'total_calls' => $totalCalls,
                'answered_calls' => $answeredCalls,
                'busy_calls' => $busyCalls,
                'no_answer_calls' => $noAnswerCalls,
                'total_duration' => $formattedTotalDuration,
            ],
            'calls' => $formattedCalls,
        ];

        // Ensure DomPDF wrapper is registered in container even if cache/packages.php is stale on live server
        if (!app()->bound('dompdf.wrapper')) {
            (new \Barryvdh\DomPDF\ServiceProvider(app()))->register();
        }

        $pdf = Pdf::loadView('call_logs.pdf', $viewData)
            ->setPaper('a4', 'landscape');

        $dateSlug = $hasDateFilter ? str_replace([' ', '-', '/'], '_', $dateRangeText) : 'all_time';
        $fileName = 'call_report_' . $dateSlug . '_' . date('Ymd_His') . '.pdf';

        // Support JSON response with base64 for mobile apps if requested
        if ($request->input('format') === 'json' || $request->boolean('base64')) {
            $pdfContent = $pdf->output();
            return response()->json([
                'status' => true,
                'message' => 'Call report PDF generated successfully.',
                'filter_mode' => $filterMode,
                'date_range' => $dateRangeText,
                'total_records' => $totalCalls,
                'filename' => $fileName,
                'pdf_base64' => base64_encode($pdfContent),
            ]);
        }

        // Stream or download
        if ($request->input('action') === 'stream' || $request->boolean('stream') || $request->input('view') === '1') {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }
}
