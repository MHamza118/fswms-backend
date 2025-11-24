<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    /** @use HasFactory<\Database\Factories\WarehouseFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'country',
        'city',
        'email',
        'zip_code',
        'address',
        'phone',
        'status',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
