<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';
    protected $primaryKey = 'lead_id';

    protected $fillable = [
        'customer_id',
        'lead_title',
        'lead_source_id',
        'lead_stage_id',
        'lead_requirement_id',
        'assigned_to',
        'priority',
        'expected_amount',
        'description',
        'next_followup_date',
        'status',
        'lost_reason_id',
        'created_by',
    ];

    protected static function booted()
    {
        static::deleting(function ($lead) {
            // Delete associated lead documents and physical files
            foreach ($lead->documents as $doc) {
                $doc->delete();
            }

            // Delete associated call recordings and physical audio files
            foreach ($lead->callRecordings as $rec) {
                $rec->delete();
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function leadSource()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id', 'lead_sources_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function leadStage()
    {
        return $this->belongsTo(LeadStage::class, 'lead_stage_id', 'lead_stage_id');
    }

    public function leadRequirement()
    {
        return $this->belongsTo(LeadRequirement::class, 'lead_requirement_id', 'lead_requirements_id');
    }

    public function lostReason()
    {
        return $this->belongsTo(LostReason::class, 'lost_reason_id', 'lost_reason_id');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class, 'lead_id', 'lead_id');
    }

    public function documents()
    {
        return $this->hasMany(LeadDocument::class, 'lead_id', 'lead_id');
    }

    public function callRecordings()
    {
        return $this->hasMany(CallRecording::class, 'lead_id', 'lead_id');
    }
}
