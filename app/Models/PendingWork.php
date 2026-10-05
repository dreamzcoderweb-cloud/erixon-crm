<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PendingWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pending_works';
    protected $primaryKey = 'pending_id';

    public const STATUS_PENDING = 0;
    public const STATUS_PROCESS = 1;
    public const STATUS_FINISHED = 2;

    protected $fillable = [
        'user_id',
        'date',
        'notes',
        'status',
    ];

    protected $casts = [
        'date'   => 'date:Y-m-d',
        'status' => 'integer',
    ];

    protected $appends = [
        'status_label',
        'status_badge',
    ];

    /**
     * Staff/User relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESS  => 'In Process',
            self::STATUS_FINISHED => 'Finished',
            default               => 'Pending',
        };
    }

    /**
     * Get badge HTML / class for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PROCESS  => '<span class="badge bg-label-info">In Process</span>',
            self::STATUS_FINISHED => '<span class="badge bg-label-success">Finished</span>',
            default               => '<span class="badge bg-label-warning">Pending</span>',
        };
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
