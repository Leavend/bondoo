<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'reference_no',
        'product_id',
        'quantity',
        'reason',
        'refund_amount',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
