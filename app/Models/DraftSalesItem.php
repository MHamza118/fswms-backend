<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftSalesItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'draft_sale_id',
        'price',
        'qty',
        'subtotal',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function draftSale()
    {
        return $this->belongsTo(DraftSale::class);
    }
}
