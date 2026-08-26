<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attendance';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'permission_start',
        'permission_end',
        'second_check_in',
        'second_check_out',
        'permission_id',
        'working_hours',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function permissionRequest()
    {
        return $this->belongsTo(PermissionRequest::class, 'permission_id', 'id');
    }
}
