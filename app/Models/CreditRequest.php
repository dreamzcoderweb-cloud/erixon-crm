<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_requests';
    protected $primaryKey = 'credit_request_id';

    protected $fillable = [
        'lead_id',
        'customer_id',
        'username',
        'phone',
        'email',
        'credit_amount',
        'is_estimate',
        'status',
        'admin_approved_by',
        'admin_approved_at',
        'admin_remarks',
        'support_approved_by',
        'support_approved_at',
        'support_remarks',
        'requested_by',
    ];

    protected $casts = [
        'credit_amount'     => 'decimal:2',
        'is_estimate'       => 'boolean',
        'admin_approved_at' => 'datetime',
        'support_approved_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by', 'id');
    }

    public function supportApprover()
    {
        return $this->belongsTo(User::class, 'support_approved_by', 'id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }

    /**
     * Scope credit requests accessible by a specific user (staff-wise data access).
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
            $q->where('requested_by', $userId)
              ->orWhereHas('customer', function ($cq) use ($user) {
                  $cq->forUser($user);
              })
              ->orWhereHas('lead', function ($lq) use ($user) {
                  $lq->forUser($user);
              });
        });
    }
}
