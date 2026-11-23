<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $auditModule = 'Leaves';

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'number_of_days',
        'leave_type',
        'reason',
        'status',
        'approval_stage',
        'approved_by',
        'admin_remarks',
        'tl_approved_by',
        'tl_approved_at',
        'tl_remarks',
        'hr_approved_by',
        'hr_approved_at',
        'hr_remarks',
        'md_approved_by',
        'md_approved_at',
        'md_remarks',
    ];

    protected $casts = [
        'from_date'       => 'date',
        'to_date'         => 'date',
        'number_of_days'  => 'float',
        'tl_approved_at'  => 'datetime',
        'hr_approved_at'  => 'datetime',
        'md_approved_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function tlApprover()
    {
        return $this->belongsTo(User::class, 'tl_approved_by', 'id');
    }

    public function hrApprover()
    {
        return $this->belongsTo(User::class, 'hr_approved_by', 'id');
    }

    public function mdApprover()
    {
        return $this->belongsTo(User::class, 'md_approved_by', 'id');
    }
}
