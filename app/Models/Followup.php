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
        'remarks',
        'next_followup_date',
        'followup_status',
        'forward_to',
        'created_by',
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
}
