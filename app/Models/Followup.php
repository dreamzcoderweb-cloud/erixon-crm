<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'followups';
    protected $primaryKey = 'followups_id';

    protected $fillable = [
        'lead_id',
        'followup_type',
        'duration',
        'remarks',
        'next_followup_date',
        'followup_status',
        'forward_to',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function forwardToUser()
    {
        return $this->belongsTo(User::class, 'forward_to', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function reassignments()
    {
        return $this->hasMany(FollowupReassignment::class, 'followup_id', 'followups_id');
    }

    /**
     * Scope follow-ups accessible by a specific user (staff-wise data access).
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
            $q->where('forward_to', $userId)
              ->orWhere(function ($q2) use ($userId) {
                  $q2->whereNull('forward_to')
                     ->where('created_by', $userId);
              })
              ->orWhere('created_by', $userId)
              ->orWhereHas('lead', function ($lq) use ($userId) {
                  $lq->where('assigned_to', $userId)
                    ->orWhere('created_by', $userId);
              });
        });
    }
}
