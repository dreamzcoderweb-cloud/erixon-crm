<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditRequestCustomField extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'credit_request_custom_fields';

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
