<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lead_sources';
    protected $primaryKey = 'lead_sources_id';

    protected $fillable = [
        'name',
        'status',
    ];

    public function leads()
    {
        return $this->hasMany(Lead::class, 'lead_source_id', 'lead_sources_id');
    }
}
