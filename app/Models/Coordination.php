<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coordination extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coordinations';
    protected $primaryKey = 'coordination_id';

    protected $fillable = [
        'staff_id',
        'link',
        'created_by',
    ];

    /**
     * Relationship to Staff (User)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    /**
     * Relationship to Creator (User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Relationship to Joining Staff Users (Pivot)
     */
    public function joiningStaff()
    {
        return $this->belongsToMany(User::class, 'coordination_joining_staff', 'coordination_id', 'user_id')
                    ->withPivot('status', 'joined_at')
                    ->withTimestamps();
    }

    /**
     * Direct relationship to Joining Staff pivot records
     */
    public function joiningStaffPivot()
    {
        return $this->hasMany(CoordinationJoiningStaff::class, 'coordination_id', 'coordination_id');
    }

    /**
     * Scope for filtering records accessible by a specific user
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
            $q->where('staff_id', $userId)
              ->orWhere('created_by', $userId)
              ->orWhereHas('joiningStaff', function ($jq) use ($userId) {
                  $jq->where('users.id', $userId);
              });
        });
    }
}
