<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadRequirementPrice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_requirement_prices';

    protected $fillable = [
        'lead_requirement_id',
        'customer_type',
        'package_name',
        'quantity',
        'unit_price_paisa',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'quantity'         => 'decimal:2',
        'unit_price_paisa' => 'decimal:4',
        'subtotal'         => 'decimal:2',
        'tax_percentage'   => 'decimal:2',
        'tax_amount'       => 'decimal:2',
        'total_amount'     => 'decimal:2',
        'status'           => 'integer',
    ];

    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    /**
     * Scope query by customer type: matches specific type or 'all'
     */
    public function scopeForCustomerType($query, ?string $customerType)
    {
        if (empty($customerType)) {
            return $query;
        }

        $type = strtolower(trim($customerType));
        return $query->where(function ($q) use ($type) {
            $q->where('customer_type', $type)
              ->orWhere('customer_type', 'all');
        });
    }
}
