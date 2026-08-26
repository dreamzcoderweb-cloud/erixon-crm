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
    ];

    protected $casts = [
        'credit_balance' => 'decimal:2',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    public function leads()
    {
        return $this->hasMany(Lead::class, 'customer_id', 'customer_id');
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
