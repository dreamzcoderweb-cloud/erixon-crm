<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_type',
        'name',
        'company_name',
        'mobile',
        'email',
        'alternate_mobile',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'created_by',
        'owner_by',
        'assign_by',
        'status',
        'credit_balance',
        'password',
        'reference_code',
        'custom_fields',
    ];

    protected $casts = [
        'credit_balance' => 'decimal:2',
        'custom_fields'  => 'array',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'owner_by_name',
        'assign_by_name',
        'created_by_name',
        'owner_name',
        'assign_name',
        'assigned_by_name',
        'creator_name',
    ];

    public function getOwnerByNameAttribute()
    {
        return $this->owner?->name;
    }

    public function getOwnerNameAttribute()
    {
        return $this->owner?->name;
    }

    public function getAssignByNameAttribute()
    {
        return $this->assignedBy?->name ?? $this->assignBy?->name;
    }

    public function getAssignNameAttribute()
    {
        return $this->assignedBy?->name ?? $this->assignBy?->name;
    }

    public function getAssignedByNameAttribute()
    {
        return $this->assignedBy?->name ?? $this->assignBy?->name;
    }

    public function getCreatedByNameAttribute()
    {
        return $this->creator?->name;
    }

    public function getCreatorNameAttribute()
    {
        return $this->creator?->name;
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_by');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assign_by');
    }

    public function assignBy()
    {
        return $this->belongsTo(User::class, 'assign_by');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'customer_id', 'customer_id');
    }

    public function latestLead()
    {
        return $this->hasOne(Lead::class, 'customer_id', 'customer_id')->latestOfMany('lead_id');
    }

    public function creditRequests()
    {
        return $this->hasMany(CreditRequest::class, 'customer_id', 'customer_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'customer_id', 'customer_id');
    }

    /**
     * Scope customers accessible by a specific user (staff-wise data access).
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

        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhereHas('leads', function ($lq) use ($userId) {
                  $lq->where('assigned_to', $userId)
                    ->orWhere('created_by', $userId)
                    ->orWhereHas('followups', function ($fq) use ($userId) {
                        $fq->where('forward_to', $userId)
                          ->orWhere('created_by', $userId);
                    });
              });
        });
    }
}
