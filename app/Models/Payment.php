<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments';
    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'customer_id',
        'lead_id',
        'lead_source_id',
        'amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'payment_method',
        'payment_date',
        'payment_screenshot',
        'tax_number',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'payment_date'   => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'lead_sources_id');
    }

    /**
     * Scope payments accessible by a specific user (staff-wise data access).
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin()) {
            return $query;
        }

        $userId = $user->id;

        return $query->where(function ($q) use ($userId, $user) {
            $q->where('created_by', $userId)
              ->orWhereHas('customer', function ($cq) use ($user) {
                  $cq->forUser($user);
              })
              ->orWhereHas('lead', function ($lq) use ($user) {
                  $lq->forUser($user);
              });
        });
    }
}
