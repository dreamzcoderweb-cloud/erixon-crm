<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoordinationJoiningStaff extends Model
{
    use HasFactory;

    protected $table = 'coordination_joining_staff';

    protected $fillable = [
        'coordination_id',
        'user_id',
        'status',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function coordination()
    {
        return $this->belongsTo(Coordination::class, 'coordination_id', 'coordination_id');
    }
}
