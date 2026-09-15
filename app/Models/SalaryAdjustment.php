<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryAdjustment extends Model
{
    use HasFactory;

    protected $table = 'salary_adjustments';

    protected $fillable = [
        'user_id',
        'month',
        'ot_income',
        'leave_deduction',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'ot_income'       => 'float',
        'leave_deduction' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
