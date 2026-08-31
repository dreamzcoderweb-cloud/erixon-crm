<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupCustomField extends Model
{
    use HasFactory;

    protected $table = 'followup_custom_fields';

    protected $fillable = [
        'field_label',
        'field_name',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'status',
    ];
}
