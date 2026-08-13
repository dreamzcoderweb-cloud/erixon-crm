<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('customers.view');
    }

    public function listData()
    {
        $customers = Customer::with('creator:id,name')
            ->orderBy('customer_id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $customers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_type' => ['required', 'in:user,reseller'],
            'name'          => ['required', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
            'mobile'        => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->withoutTrashed()],
            'email'         => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'pincode'       => ['nullable', 'string', 'max:20'],
            'status'        => ['required', 'in:0,1'],
        ]);

        $validated['created_by'] = Auth::id();

        $customer = Customer::create($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer created successfully.',
            'data'    => $customer
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $customer
        ]);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found.'
            ], 404);
        }

        $validated = $request->validate([
            'customer_type' => ['required', 'in:user,reseller'],
            'name'          => ['required', 'string', 'max:255'],
            'company_name'  => ['nullable', 'string', 'max:255'],
            'mobile'        => ['required', 'string', 'max:20', Rule::unique('customers', 'mobile')->ignore($customer->customer_id, 'customer_id')->withoutTrashed()],
            'email'         => ['nullable', 'email', 'max:255'],
            'alternate_mobile' => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string'],
            'city'          => ['nullable', 'string', 'max:100'],
            'state'         => ['nullable', 'string', 'max:100'],
            'country'       => ['nullable', 'string', 'max:100'],
            'pincode'       => ['nullable', 'string', 'max:20'],
            'status'        => ['required', 'in:0,1'],
        ]);

        $customer->update($validated);

        return response()->json([
            'status'  => true,
            'message' => 'Customer updated successfully.',
            'data'    => $customer
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::find($id);
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
        $customer = Customer::find($id);
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
}
