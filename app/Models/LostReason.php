<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LostReason extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lost_reasons';
    protected $primaryKey = 'lost_reason_id';

    protected $fillable = [
        'reason',
        'status',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'lost_reason_id', 'lost_reason_id');
    }
}
