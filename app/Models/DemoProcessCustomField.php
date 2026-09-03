<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DemoProcessCustomField extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'demo_process_custom_fields';

    protected $fillable = [
        'field_label',
        'field_name',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];
}
