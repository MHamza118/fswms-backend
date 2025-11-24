<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'product_image',
        'sku',
        'barcode',
        'category_id',
        'unit_id',
        'warehouse_id',
        'brand_id',
        'qty_alert',
        'stock_quantity',
        'discount',
        'tax',
        'purchase_price',
        'selling_price',
        'description',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function SaleItem()
    {
        return $this->hasMany(SaleItem::class);
    }
    
    public function deviceAttribute()
{
    return $this->hasOne(DeviceAttribute::class, 'product_id');
}
}
