<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'event',
        'auditable_type',
        'auditable_id',
        'module',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the associated auditable model.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by period or date range.
     */
    public function scopeFilterPeriod($query, $filterType, $date = null, $month = null, $startDate = null, $endDate = null)
    {
        if ($filterType === 'today') {
            return $query->whereDate('created_at', Carbon::today());
        }

        if ($filterType === 'yesterday') {
            return $query->whereDate('created_at', Carbon::yesterday());
        }

        if ($filterType === 'this_week') {
            return $query->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        }

        if ($filterType === 'this_month') {
            return $query->whereBetween('created_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ]);
        }

        if ($filterType === 'custom') {
            if (!empty($startDate) && !empty($endDate)) {
                return $query->whereBetween('created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay(),
                ]);
            }

            if (!empty($startDate)) {
                return $query->whereDate('created_at', '>=', Carbon::parse($startDate));
            }

            if (!empty($endDate)) {
                return $query->whereDate('created_at', '<=', Carbon::parse($endDate));
            }
        }

        if ($filterType === 'date' && !empty($date)) {
            return $query->whereDate('created_at', Carbon::parse($date));
        }

        if ($filterType === 'month' && !empty($month)) {
            $parsed = Carbon::parse($month . '-01');
            return $query->whereYear('created_at', $parsed->year)
                ->whereMonth('created_at', $parsed->month);
        }

        return $query;
    }
}
