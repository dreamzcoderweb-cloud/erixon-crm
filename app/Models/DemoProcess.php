<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoProcess extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demo_processes';
    protected $primaryKey = 'demo_process_id';

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'lead_source_id',
        'product_names',
        'demo_date',
        'demo_time',
        'customer_type',
        'created_by',
        'assigned_by',
        'sub_assigned_by',
        'status',
        'remarks',
    ];

    protected $casts = [
        'product_names' => 'array',
        'demo_date'     => 'date',
    ];

    /**
     * Relationship to Creator (Sales Staff)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Relationship to Assigned User (Product Manager)
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    /**
     * Relationship to Sub Assigned User (Support Team)
     */
    public function subAssignedUser()
    {
        return $this->belongsTo(User::class, 'sub_assigned_by', 'id');
    }

    /**
     * Relationship to Lead Source
     */
    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'lead_sources_id');
    }

    /**
     * User Visibility Scoping
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
            $q->where('created_by', $userId)
              ->orWhere('assigned_by', $userId)
              ->orWhere('sub_assigned_by', $userId);
        });
    }
}
