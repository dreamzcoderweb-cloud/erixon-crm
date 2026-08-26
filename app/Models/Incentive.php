<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incentive extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'incentives';
    protected $primaryKey = 'incentive_id';

    protected $fillable = [
        'staff_id',
        'month',
        'amount',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
