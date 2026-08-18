<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'templates';
    protected $primaryKey = 'template_id';

    protected $fillable = [
        'type',
        'title',
        'content',
        'status',
    ];
}
