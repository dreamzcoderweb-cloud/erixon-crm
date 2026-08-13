<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_requirements';
    protected $primaryKey = 'lead_requirements_id';

    protected $fillable = [
        'name',
        'status',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'lead_requirement_id', 'lead_requirements_id');
    }
}
