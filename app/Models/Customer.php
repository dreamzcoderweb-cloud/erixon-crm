<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Auditable;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    protected static function booted()
    {
        static::deleting(function ($customer) {
            if (!$customer->isForceDeleting() && !empty($customer->mobile) && !str_contains($customer->mobile, '_d')) {
                $suffix = '_d' . $customer->customer_id;
                $maxLen = 20 - strlen($suffix);
                $customer->mobile = substr($customer->mobile, 0, $maxLen) . $suffix;
                $customer->saveQuietly();
            }
        });
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_type',
        'branch_id',
        'team_id',
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
        'lead_requirement_id',
        'lead_stage_id',
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
        'branch_name',
        'team_name',
        'owner_by_name',
        'assign_by_name',
        'created_by_name',
        'owner_name',
        'assign_name',
        'assigned_by_name',
        'creator_name',
        'lead_requirement_name',
        'lead_stage_name',
    ];

    public function getBranchNameAttribute()
    {
        return $this->branch?->name;
    }

    public function getTeamNameAttribute()
    {
        return $this->team?->name;
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_customer', 'customer_id', 'branch_id')->withPivot('is_primary')->withTimestamps();
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'customer_team', 'customer_id', 'team_id')->withPivot('is_primary')->withTimestamps();
    }

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

    public function followups()
    {
        return $this->hasMany(Followup::class, 'customer_id', 'customer_id');
    }

    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id', 'lead_stage_id');
    }

    public function getLeadRequirementNameAttribute()
    {
        return $this->leadRequirement?->name ?? $this->latestLead?->leadRequirement?->name;
    }

    public function getLeadStageNameAttribute()
    {
        return $this->leadStage?->name ?? $this->latestLead?->leadStage?->name;
    }

    /**
     * Scope customers accessible by a specific user (staff-wise data access).
     */
    public function scopeForUser($query, $user)
    {
        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        // Branch Manager scope: sees all customers in their branch(es)
        if ($user->isBranchManager()) {
            $branchIds = $user->accessibleBranchIds();
            if (!empty($branchIds)) {
                return $query->where(function ($q) use ($branchIds) {
                    $q->whereIn('customers.branch_id', $branchIds)
                      ->orWhereHas('branches', fn($bq) => $bq->whereIn('branches.id', $branchIds));
                });
            }
        }

        // Team Leader scope: sees all customers in their team(s)
        if ($user->isTeamLeader()) {
            $teamIds = $user->accessibleTeamIds();
            if (!empty($teamIds)) {
                return $query->where(function ($q) use ($teamIds) {
                    $q->whereIn('customers.team_id', $teamIds)
                      ->orWhereHas('teams', fn($tq) => $tq->whereIn('teams.id', $teamIds));
                });
            }
        }

        // Staff scope: own customers (created, owned, or assigned)
        $userId = $user->id;

        return $query->where(function ($q) use ($userId) {
            $q->where('customers.created_by', $userId)
              ->orWhere('customers.owner_by', $userId)
              ->orWhere('customers.assign_by', $userId);
        });
    }

    /**
     * Scope customers who qualify for the customer list:
     * If a customer has leads, at least one lead must be in the "Sale closed" stage.
     * Customers without any leads (direct customers) are also included.
     */
    public function scopeQualifiedForCustomerList($query)
    {
        return $query->where(function ($q) {
            $q->doesntHave('leads')
              ->orWhereHas('leads.leadStage', function ($sq) {
                  $sq->whereRaw('LOWER(name) LIKE ?', ['%sale%close%']);
              });
        });
    }
}

