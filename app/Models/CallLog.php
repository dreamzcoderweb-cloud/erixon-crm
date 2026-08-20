<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog extends Model
{
    use HasFactory;

    protected $table = 'call_logs';
    protected $primaryKey = 'call_id';

    public const UPDATED_AT = null;

    protected $fillable = [
        'lead_id',
        'user_id',
        'phone',
        'call_type',
        'duration',
        'call_status',
        'recording_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function recording()
    {
        return $this->belongsTo(CallRecording::class, 'recording_id', 'call_id');
    }
}
