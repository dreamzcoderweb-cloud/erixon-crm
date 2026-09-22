<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LeadRequirement;
use App\Models\LeadSource;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData($request);
        }

        $user = Auth::user();
        $data['customers']        = Customer::forUser($user)->where('status', 1)->orderBy('name')->get();
        $data['leadSources']      = LeadSource::where('status', 1)->orderBy('lead_sources_id')->get();
        $data['leadRequirements'] = LeadRequirement::where('status', 1)->orderBy('name')->get();

        return view('payments.view', $data);
    }

    public function listData(Request $request = null)
    {
        $request = $request ?? request();

        $query = Payment::forUser(Auth::user())->with([
            'customer:customer_id,name,mobile,email',
            'lead:lead_id,lead_title',
            'leadSource:lead_sources_id,name',
            'leadRequirement:lead_requirements_id,name',
            'creator:id,name'
        ]);

        $filterType = $request->input('filter_type');
        $date       = $request->input('date');
        $month      = $request->input('month');
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');

        if ($filterType === 'daily' && !empty($date)) {
            $query->whereDate('payment_date', $date);
        } elseif ($filterType === 'weekly') {
            $refDate = !empty($startDate) ? Carbon::parse($startDate) : Carbon::today();
            $query->whereBetween('payment_date', [
                $refDate->copy()->startOfWeek()->toDateString(),
                $refDate->copy()->endOfWeek()->toDateString(),
            ]);
        } elseif ($filterType === 'monthly' && !empty($month)) {
            [$year, $selectedMonth] = array_pad(explode('-', $month), 2, null);
            $query->whereYear('payment_date', $year ?: date('Y'))
                ->whereMonth('payment_date', $selectedMonth ?: date('m'));
        } elseif ($filterType === 'custom') {
            if (!empty($startDate)) {
                $query->whereDate('payment_date', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('payment_date', '<=', $endDate);
            }
        }

        if ($request->filled('lead_requirement_id')) {
            $reqId = $request->input('lead_requirement_id');
            $query->where(function ($q) use ($reqId) {
                $q->where('lead_requirement_id', $reqId)
                  ->orWhereHas('lead', function ($lq) use ($reqId) {
                      $lq->where('lead_requirement_id', $reqId);
                  });
            });
        }

        $payments = $query->orderBy('payment_id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'data'   => $payments
        ]);
    }

    /**
     * Requirement 10: Store payment with Mandatory Tax (Tax % and Tax Amount) & Screenshot upload
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'tax_percentage'      => ['required', 'numeric', 'min:0'],
            'tax_amount'          => ['required', 'numeric', 'min:0'],
            'total_amount'        => ['required', 'numeric', 'min:0.01'],
            'payment_method'      => ['required', 'string', 'max:50'],
            'payment_date'        => ['required', 'date'],
            'tax_number'          => ['nullable', 'string', 'max:100'],
            'remarks'             => ['nullable', 'string'],
            'payment_screenshot'  => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ], [
            'tax_percentage.required' => 'Tax percentage is mandatory.',
            'tax_amount.required'     => 'Tax amount is mandatory.',
            'total_amount.required'   => 'Total amount including tax is mandatory.',
        ]);

        $filePath = null;
        if ($request->hasFile('payment_screenshot')) {
            $file = $request->file('payment_screenshot');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/payments');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $filePath = 'uploads/payments/' . $fileName;
        }

        $payment = Payment::create([
            'customer_id'         => $validated['customer_id'],
            'lead_source_id'      => $validated['lead_source_id'] ?? null,
            'lead_requirement_id' => $validated['lead_requirement_id'] ?? null,
            'amount'              => $validated['amount'],
            'tax_percentage'      => $validated['tax_percentage'],
            'tax_amount'          => $validated['tax_amount'],
            'total_amount'        => $validated['total_amount'],
            'payment_method'      => $validated['payment_method'],
            'payment_date'        => $validated['payment_date'],
            'payment_screenshot'  => $filePath,
            'tax_number'          => $validated['tax_number'] ?? null,
            'remarks'             => $validated['remarks'] ?? null,
            'created_by'          => Auth::id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Payment record saved successfully.',
            'data'    => $payment
        ]);
    }

    public function edit($id)
    {
        $payment = Payment::forUser(Auth::user())->with(['customer', 'leadSource', 'leadRequirement', 'creator'])->find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment record not found.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $payment
        ]);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::forUser(Auth::user())->find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment record not found.'], 404);
        }

        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,customer_id'],
            'lead_source_id'      => ['nullable', 'exists:lead_sources,lead_sources_id'],
            'lead_requirement_id' => ['nullable', 'exists:lead_requirements,lead_requirements_id'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'tax_percentage'      => ['required', 'numeric', 'min:0'],
            'tax_amount'          => ['required', 'numeric', 'min:0'],
            'total_amount'        => ['required', 'numeric', 'min:0.01'],
            'payment_method'      => ['required', 'string', 'max:50'],
            'payment_date'        => ['required', 'date'],
            'tax_number'          => ['nullable', 'string', 'max:100'],
            'remarks'             => ['nullable', 'string'],
            'payment_screenshot'  => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        if ($request->hasFile('payment_screenshot')) {
            if (!empty($payment->payment_screenshot) && file_exists(public_path($payment->payment_screenshot))) {
                @unlink(public_path($payment->payment_screenshot));
            }

            $file = $request->file('payment_screenshot');
            $fileName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $destinationPath = public_path('uploads/payments');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $fileName);
            $payment->payment_screenshot = 'uploads/payments/' . $fileName;
        }

        $payment->customer_id         = $validated['customer_id'];
        $payment->lead_source_id      = $validated['lead_source_id'] ?? null;
        $payment->lead_requirement_id = $validated['lead_requirement_id'] ?? null;
        $payment->amount              = $validated['amount'];
        $payment->tax_percentage      = $validated['tax_percentage'];
        $payment->tax_amount          = $validated['tax_amount'];
        $payment->total_amount        = $validated['total_amount'];
        $payment->payment_method      = $validated['payment_method'];
        $payment->payment_date        = $validated['payment_date'];
        $payment->tax_number          = $validated['tax_number'] ?? null;
        $payment->remarks             = $validated['remarks'] ?? null;

        $payment->save();

        return response()->json([
            'status'  => true,
            'message' => 'Payment record updated successfully.',
            'data'    => $payment
        ]);
    }

    public function destroy($id)
    {
        $payment = Payment::forUser(Auth::user())->find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment record not found.'], 404);
        }

        if (!empty($payment->payment_screenshot) && file_exists(public_path($payment->payment_screenshot))) {
            @unlink(public_path($payment->payment_screenshot));
        }

        $payment->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Payment record deleted successfully.'
        ]);
    }
}
