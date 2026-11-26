<?php

namespace App\Http\Controllers;

use App\Models\LeadRequirement;
use App\Models\LeadRequirementPrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LeadRequirementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->listData();
        }

        return view('lead_requirements.view');
    }

    public function listData()
    {
        $requirements = LeadRequirement::with('prices')
            ->withCount('prices')
            ->orderBy('lead_requirements_id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $requirements
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255', Rule::unique('lead_requirements', 'name')->withoutTrashed()],
            'status'                    => ['required', 'in:0,1'],
            'prices'                    => ['nullable', 'array'],
            'prices.*.customer_type'    => ['nullable', 'in:all,user,reseller'],
            'prices.*.package_name'     => ['nullable', 'string', 'max:255'],
            'prices.*.quantity'         => ['nullable', 'numeric', 'min:0'],
            'prices.*.unit_price_paisa' => ['nullable', 'numeric', 'min:0'],
            'prices.*.subtotal'         => ['nullable', 'numeric', 'min:0'],
            'prices.*.tax_percentage'   => ['nullable', 'numeric', 'min:0'],
            'prices.*.tax_amount'       => ['nullable', 'numeric', 'min:0'],
            'prices.*.total_amount'     => ['nullable', 'numeric', 'min:0'],
            'prices.*.status'           => ['nullable', 'in:0,1'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $requirement = LeadRequirement::create([
                'name'   => $validated['name'],
                'status' => $validated['status'],
            ]);

            $this->syncPrices($requirement, $request->input('prices', []));

            return response()->json([
                'status'  => true,
                'message' => 'Lead requirement created successfully.',
                'data'    => $requirement->load('prices')
            ]);
        });
    }

    public function edit($id)
    {
        $requirement = LeadRequirement::with('prices')->find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $requirement
        ]);
    }

    public function update(Request $request, $id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $validated = $request->validate([
            'name'                      => ['required', 'string', 'max:255', Rule::unique('lead_requirements', 'name')->ignore($requirement->lead_requirements_id, 'lead_requirements_id')->withoutTrashed()],
            'status'                    => ['required', 'in:0,1'],
            'prices'                    => ['nullable', 'array'],
            'prices.*.customer_type'    => ['nullable', 'in:all,user,reseller'],
            'prices.*.package_name'     => ['nullable', 'string', 'max:255'],
            'prices.*.quantity'         => ['nullable', 'numeric', 'min:0'],
            'prices.*.unit_price_paisa' => ['nullable', 'numeric', 'min:0'],
            'prices.*.subtotal'         => ['nullable', 'numeric', 'min:0'],
            'prices.*.tax_percentage'   => ['nullable', 'numeric', 'min:0'],
            'prices.*.tax_amount'       => ['nullable', 'numeric', 'min:0'],
            'prices.*.total_amount'     => ['nullable', 'numeric', 'min:0'],
            'prices.*.status'           => ['nullable', 'in:0,1'],
        ]);

        return DB::transaction(function () use ($requirement, $validated, $request) {
            $requirement->update([
                'name'   => $validated['name'],
                'status' => $validated['status'],
            ]);

            $this->syncPrices($requirement, $request->input('prices', []));

            return response()->json([
                'status'  => true,
                'message' => 'Lead requirement updated successfully.',
                'data'    => $requirement->load('prices')
            ]);
        });
    }

    protected function syncPrices(LeadRequirement $requirement, array $prices)
    {
        // Delete old prices and re-insert active ones
        $requirement->prices()->forceDelete();

        $rowsToInsert = [];
        foreach ($prices as $p) {
            if (empty($p)) continue;
            $qty = isset($p['quantity']) && is_numeric($p['quantity']) ? (float)$p['quantity'] : 0;
            $pricePaisa = isset($p['unit_price_paisa']) && is_numeric($p['unit_price_paisa']) ? (float)$p['unit_price_paisa'] : 0;
            $pkgName = trim((string)($p['package_name'] ?? ''));

            // Only skip completely empty rows
            if ($qty <= 0 && $pricePaisa <= 0 && empty($pkgName)) {
                continue;
            }

            $subtotal = isset($p['subtotal']) && is_numeric($p['subtotal']) && (float)$p['subtotal'] > 0
                ? (float)$p['subtotal']
                : round($qty * $pricePaisa, 2);

            $taxPct = isset($p['tax_percentage']) && is_numeric($p['tax_percentage'])
                ? (float)$p['tax_percentage']
                : 18.0;

            $taxAmt = isset($p['tax_amount']) && is_numeric($p['tax_amount']) && (float)$p['tax_amount'] > 0
                ? (float)$p['tax_amount']
                : round(($subtotal * $taxPct) / 100, 2);

            $totalAmt = isset($p['total_amount']) && is_numeric($p['total_amount']) && (float)$p['total_amount'] > 0
                ? (float)$p['total_amount']
                : round($subtotal + $taxAmt, 2);

            $rowsToInsert[] = [
                'lead_requirement_id' => $requirement->lead_requirements_id,
                'customer_type'       => in_array($p['customer_type'] ?? 'all', ['all', 'user', 'reseller']) ? $p['customer_type'] : 'all',
                'package_name'        => $pkgName ?: ($qty > 0 ? (string)$qty : 'Package'),
                'quantity'            => $qty,
                'unit_price_paisa'    => $pricePaisa,
                'subtotal'            => $subtotal,
                'tax_percentage'      => $taxPct,
                'tax_amount'          => $taxAmt,
                'total_amount'        => $totalAmt,
                'status'              => isset($p['status']) ? (int)$p['status'] : 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        if (!empty($rowsToInsert)) {
            LeadRequirementPrice::insert($rowsToInsert);
        }
    }

    public function getPrices(Request $request, $id)
    {
        $customerType = $request->query('customer_type');
        $prices = LeadRequirementPrice::where('lead_requirement_id', $id)
            ->where('status', 1)
            ->forCustomerType($customerType)
            ->orderBy('quantity', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $prices
        ]);
    }

    public function destroy($id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $requirement->prices()->delete();
        $requirement->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement deleted successfully.'
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        $requirement = LeadRequirement::find($id);
        if (!$requirement) {
            return response()->json([
                'status'  => false,
                'message' => 'Lead requirement not found.'
            ], 404);
        }

        $requirement->status = $requirement->status == 1 ? 0 : 1;
        $requirement->save();

        return response()->json([
            'status'  => true,
            'message' => 'Lead requirement status updated successfully.',
            'new_status' => $requirement->status
        ]);
    }
}
