<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'teams';

    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'leader_id',
        'description',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'team_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'team_id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'team_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role_in_team', 'is_primary')
            ->withTimestamps();
    }
}
