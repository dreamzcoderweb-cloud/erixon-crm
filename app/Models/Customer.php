<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'customers';
    protected $primaryKey = 'customer_id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'customer_type',
        'name',
        'company_name',
        'mobile',
        'email',
        'alternate_mobile',
        'address',
        'city',
        'state',
        'country',
        'pincode',
        'created_by',
        'status',
        'password',
        'reference_code',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'customer_id', 'customer_id');
    }
}
