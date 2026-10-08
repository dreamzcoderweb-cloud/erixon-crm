<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'proposals';
    protected $primaryKey = 'proposal_id';
    protected $auditModule = 'Proposal';

    protected $fillable = [
        'proposal_number',
        'customer_id',
        'customer_name',
        'customer_type',
        'customer_mobile',
        'customer_email',
        'email',
        'lead_requirement_id',
        'lead_requirement_name',
        'sales_manager_id',
        'sales_manager_name',
        'sales_manager_mobile',
        'notes',
        'subtotal',
        'total_tax',
        'total_amount',
        'status',
        'created_by',
    ];

    public function setEmailAttribute($value)
    {
        $this->attributes['customer_email'] = $value;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['customer_email'] ?? null;
    }

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'total_tax'    => 'decimal:2',
        'total_amount' => 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    protected static function booted()
    {
        static::deleting(function ($proposal) {
            if ($proposal->isForceDeleting()) {
                $proposal->items()->forceDelete();
            } else {
                $proposal->items()->delete();
            }
        });

        static::restoring(function ($proposal) {
            $proposal->items()->restore();
        });
    }

    public function items()
    {
        return $this->hasMany(ProposalItem::class, 'proposal_id', 'proposal_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    public function salesManager()
    {
        return $this->belongsTo(User::class, 'sales_manager_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Scope for User-based data isolation
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        $userId = $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('sales_manager_id', $userId)
              ->orWhere('created_by', $userId);
        });
    }
}
