<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'code',
        'manager_id',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'pincode',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'branch_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'branch_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'branch_id');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'branch_user')
            ->withPivot('is_primary')
            ->withTimestamps();
    }
}
