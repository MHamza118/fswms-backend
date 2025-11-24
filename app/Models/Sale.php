<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'warehouse_id',
        'tax',
        'discount',
        'shipping_cost',
        'grand_total',
        'paid',
        'due',
        'payment_status',
        'currency',
        'description',
        'expected_delivery_date',
        'payment_method',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}

