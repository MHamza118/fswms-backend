<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'warehouse_id',
        // 'grand_total',
        'date',
        'user_id',
        'status',
    ];

    public function draftSaleItems()
{
    return $this->hasMany(DraftSalesItem::class);
}

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
