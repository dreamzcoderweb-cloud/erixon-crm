<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_stages';
    protected $primaryKey = 'lead_stage_id';

    protected $fillable = [
        'name',
        'sort_order',
        'status',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'lead_stage_id', 'lead_stage_id');
    }
}
