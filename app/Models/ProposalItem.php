<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProposalItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proposal_items';

    protected $fillable = [
        'proposal_id',
        'package',
        'product_package',
        'price',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'amount',
    ];

    protected $casts = [
        'package'        => 'decimal:2',
        'price'          => 'decimal:4',
        'subtotal'       => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount'     => 'decimal:2',
        'amount'         => 'decimal:2',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id', 'proposal_id');
    }
}
