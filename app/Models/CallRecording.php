<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallRecording extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'call_recordings';
    protected $primaryKey = 'call_id';

    protected $fillable = [
        'lead_id',
        'recording_file',
        'duration',
        'created_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($recording) {
            if (!empty($recording->recording_file)) {
                delete_file($recording->recording_file);
            }
        });
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id', 'lead_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
