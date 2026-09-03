<?php

namespace App\Http\Controllers;

use App\Models\DemoProcess;
use App\Models\DemoProcessCustomField;
use App\Models\LeadSource;
use App\Models\Customer;
use App\Models\LeadRequirement;
use App\Models\User;
use App\Notifications\DemoProcessCreated;
use App\Notifications\DemoProcessPending;
use App\Notifications\DemoProcessFinished;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DemoProcessController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $staffList = User::orderBy('name', 'asc')->get();

        // Product Managers & Support Team staff list
        $productManagers = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%Product Manager%')
              ->orWhere('name', 'like', '%Super Admin%')
              ->orWhere('name', 'like', '%Admin%');
        })->orWhere('id', 1)->orderBy('name', 'asc')->get();

        if ($productManagers->isEmpty()) {
            $productManagers = $staffList;
        }

        $supportTeam = User::whereHas('roles', function ($q) {
            $q->where('name', 'like', '%Support%')
              ->orWhere('name', 'like', '%Product Manager%')
              ->orWhere('name', 'like', '%Super Admin%')
              ->orWhere('name', 'like', '%Admin%');
        })->orWhere('id', 1)->orderBy('name', 'asc')->get();

        if ($supportTeam->isEmpty()) {
            $supportTeam = $staffList;
        }

        $leadSources = LeadSource::orderBy('name', 'asc')->get();
        $customers = Customer::orderBy('name', 'asc')->get();
        $leadRequirements = LeadRequirement::orderBy('name', 'asc')->get();
        $customFields = DemoProcessCustomField::where('status', 1)->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        return view('demo_processes.view', compact('staffList', 'productManagers', 'supportTeam', 'leadSources', 'customers', 'leadRequirements', 'customFields'));
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();
        $user = Auth::user();
        $query = DemoProcess::forUser($user);

        if ($request->filled('status')) {
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

        $demoProcesses = $query->with([
                'creator:id,name,email',
                'assignedUser:id,name,email',
                'subAssignedUser:id,name,email',
                'leadSource:lead_sources_id,name'
            ])
            ->orderBy('demo_process_id', 'DESC')
            ->get();

        $data = $demoProcesses->map(function ($dp) {
            return [
                'demo_process_id' => $dp->demo_process_id,
                'customer_name'   => $dp->customer_name,
                'customer_phone'  => $dp->customer_phone,
                'lead_source_id'  => $dp->lead_source_id,
                'lead_source'     => $dp->leadSource ? $dp->leadSource->name : 'N/A',
                'product_names'   => $dp->product_names ?? [],
                'product_text'    => is_array($dp->product_names) ? implode(', ', $dp->product_names) : ($dp->product_names ?? 'N/A'),
                'demo_date'       => $dp->demo_date ? $dp->demo_date->format('Y-m-d') : null,
                'demo_date_formatted' => $dp->demo_date ? $dp->demo_date->format('d/m/Y') : 'N/A',
                'demo_time'       => $dp->demo_time,
                'customer_type'   => $dp->customer_type ?? 'N/A',
                'created_by'      => $dp->created_by,
                'creator'         => $dp->creator,
                'assigned_by'     => $dp->assigned_by,
                'assigned_user'   => $dp->assignedUser,
                'sub_assigned_by' => $dp->sub_assigned_by,
                'sub_assigned_user' => $dp->subAssignedUser,
                'status'          => $dp->status,
                'remarks'         => $dp->remarks,
                'created_at'      => $dp->created_at ? $dp->created_at->format('d/m/Y H:i') : '',
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $data,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string|max:30',
            'lead_source_id'  => 'nullable|exists:lead_sources,lead_sources_id',
            'product_names'   => 'nullable',
            'demo_date'       => 'required|date',
            'demo_time'       => 'required|string',
            'customer_type'   => 'nullable|string|max:100',
            'assigned_by'     => 'nullable|exists:users,id',
            'sub_assigned_by' => 'nullable|exists:users,id',
            'remarks'         => 'nullable|string',
        ];

        $requiredCustomFields = DemoProcessCustomField::where('status', 1)->where('is_required', 'Yes')->get();
        $customAttributes = [];
        foreach ($requiredCustomFields as $rcf) {
            $rules["custom_fields.{$rcf->field_name}"] = 'required';
            $customAttributes["custom_fields.{$rcf->field_name}"] = $rcf->field_label;
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $rawProducts = $request->input('product_names');
        $productArray = is_array($rawProducts) ? $rawProducts : (!empty($rawProducts) ? [$rawProducts] : []);

        $demoProcess = DemoProcess::create([
            'customer_name'   => $request->input('customer_name'),
            'customer_phone'  => $request->input('customer_phone'),
            'lead_source_id'  => $request->filled('lead_source_id') ? $request->input('lead_source_id') : null,
            'product_names'   => $productArray,
            'demo_date'       => $request->input('demo_date'),
            'demo_time'       => $request->input('demo_time'),
            'customer_type'   => $request->input('customer_type'),
            'created_by'      => $user->id,
            'assigned_by'     => $request->filled('assigned_by') ? $request->input('assigned_by') : null,
            'sub_assigned_by' => $request->filled('sub_assigned_by') ? $request->input('sub_assigned_by') : null,
            'status'          => 'Pending',
            'remarks'         => $request->input('remarks'),
            'custom_fields'   => $request->input('custom_fields'),
        ]);

        // Send Notifications to involved recipients
        $this->sendDemoNotifications($demoProcess, 'created');

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process created successfully.',
            'data'    => $demoProcess,
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $demoProcess = DemoProcess::forUser($user)->find($id);

        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'demo_process_id' => $demoProcess->demo_process_id,
                'customer_name'   => $demoProcess->customer_name,
                'customer_phone'  => $demoProcess->customer_phone,
                'lead_source_id'  => $demoProcess->lead_source_id,
                'demo_date'       => $demoProcess->demo_date ? $demoProcess->demo_date->format('Y-m-d') : '',
                'demo_time'       => $demoProcess->demo_time,
                'customer_type'   => $demoProcess->customer_type,
                'assigned_by'     => $demoProcess->assigned_by,
                'sub_assigned_by' => $demoProcess->sub_assigned_by,
                'status'          => $demoProcess->status,
                'remarks'         => $demoProcess->remarks,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $demoProcess = DemoProcess::forUser($user)->find($id);

        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.'
            ], 404);
        }

        $rules = [
            'customer_name'   => 'required|string|max:255',
            'customer_phone'  => 'required|string|max:30',
            'lead_source_id'  => 'nullable|exists:lead_sources,lead_sources_id',
            'product_names'   => 'nullable',
            'demo_date'       => 'required|date',
            'demo_time'       => 'required|string',
            'customer_type'   => 'nullable|string|max:100',
            'assigned_by'     => 'nullable|exists:users,id',
            'sub_assigned_by' => 'nullable|exists:users,id',
            'status'          => 'required|in:Pending,Finished',
            'remarks'         => 'nullable|string',
        ];

        $requiredCustomFields = DemoProcessCustomField::where('status', 1)->where('is_required', 'Yes')->get();
        $customAttributes = [];
        foreach ($requiredCustomFields as $rcf) {
            $rules["custom_fields.{$rcf->field_name}"] = 'required';
            $customAttributes["custom_fields.{$rcf->field_name}"] = $rcf->field_label;
        }

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldStatus = $demoProcess->status;
        $newStatus = $request->input('status');



        $rawProducts = $request->input('product_names');
        $productArray = is_array($rawProducts) ? $rawProducts : (!empty($rawProducts) ? [$rawProducts] : []);

        $demoProcess->update([
            'customer_name'   => $request->input('customer_name'),
            'customer_phone'  => $request->input('customer_phone'),
            'lead_source_id'  => $request->filled('lead_source_id') ? $request->input('lead_source_id') : null,
            'product_names'   => $productArray,
            'demo_date'       => $request->input('demo_date'),
            'demo_time'       => $request->input('demo_time'),
            'customer_type'   => $request->input('customer_type'),
            'assigned_by'     => $request->filled('assigned_by') ? $request->input('assigned_by') : null,
            'sub_assigned_by' => $request->filled('sub_assigned_by') ? $request->input('sub_assigned_by') : null,
            'status'          => $newStatus,
            'remarks'         => $request->input('remarks'),
            'custom_fields'   => $request->input('custom_fields'),
        ]);

        if ($oldStatus !== 'Finished' && $newStatus === 'Finished') {
            $this->sendDemoNotifications($demoProcess, 'finished');
        }

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process updated successfully.',
            'data'    => $demoProcess,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $demoProcess = DemoProcess::forUser($user)->find($id);

        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.'
            ], 404);
        }

        $newStatus = $request->input('status');
        if (!in_array($newStatus, ['Pending', 'Finished'])) {
            $newStatus = ($demoProcess->status === 'Finished') ? 'Pending' : 'Finished';
        }

        $oldStatus = $demoProcess->status;
        $demoProcess->update(['status' => $newStatus]);

        if ($oldStatus !== 'Finished' && $newStatus === 'Finished') {
            $this->sendDemoNotifications($demoProcess, 'finished');
        }

        return response()->json([
            'status'     => true,
            'message'    => "Demo Process status updated to {$newStatus}.",
            'new_status' => $newStatus,
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $demoProcess = DemoProcess::forUser($user)->find($id);

        if (!$demoProcess) {
            return response()->json([
                'status'  => false,
                'message' => 'Demo Process record not found or access denied.'
            ], 404);
        }

        $demoProcess->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Demo Process deleted successfully.'
        ]);
    }

    /**
     * Send targeted notifications to involved users (deduplicated)
     */
    private function sendDemoNotifications(DemoProcess $demoProcess, string $type)
    {
        $recipientIds = array_values(array_filter(array_unique([
            $demoProcess->created_by,
            $demoProcess->assigned_by,
            $demoProcess->sub_assigned_by,
            1, // Super Admin
        ])));

        $recipients = User::whereIn('id', $recipientIds)->get()->unique('id');

        foreach ($recipients as $recipient) {
            if ($type === 'created') {
                $recipient->notify(new DemoProcessCreated($demoProcess));
                $recipient->notify(new DemoProcessPending($demoProcess));
            } elseif ($type === 'finished') {
                $recipient->notify(new DemoProcessFinished($demoProcess));
            }
        }
    }
}
