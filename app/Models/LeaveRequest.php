<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leave_requests';

    protected $fillable = [
        'user_id',
        'from_date',
        'to_date',
        'number_of_days',
        'leave_type',
        'reason',
        'status',
        'approved_by',
        'admin_remarks',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'number_of_days' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }
}
