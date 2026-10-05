<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyLearning extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daily_learnings';
    protected $primaryKey = 'daily_learning_id';

    protected $fillable = [
        'user_id',
        'date',
        'notes',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    /**
     * Staff/User relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Scope for User: SuperAdmin and Admin can see all, otherwise only their own.
     */
    public function scopeForUser($query, ?User $user = null)
    {
        if (!$user) {
            return $query;
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }
}
