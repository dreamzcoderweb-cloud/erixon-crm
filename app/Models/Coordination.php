<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coordination extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coordinations';
    protected $primaryKey = 'coordination_id';

    protected $fillable = [
        'staff_id',
        'link',
        'created_by',
    ];

    /**
     * Relationship to Staff (User)
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id', 'id');
    }

    /**
     * Relationship to Creator (User)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
