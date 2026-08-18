<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupReassignment extends Model
{
    use HasFactory;

    protected $table = 'followup_reassignments';

    protected $fillable = [
        'followup_id',
        'previous_staff_id',
        'new_staff_id',
        'reassigned_by',
        'notes',
    ];

    public function followup()
    {
        return $this->belongsTo(Followup::class, 'followup_id', 'followups_id');
    }

    public function previousStaff()
    {
        return $this->belongsTo(User::class, 'previous_staff_id', 'id');
    }

    public function newStaff()
    {
        return $this->belongsTo(User::class, 'new_staff_id', 'id');
    }

    public function reassignedBy()
    {
        return $this->belongsTo(User::class, 'reassigned_by', 'id');
    }
}
