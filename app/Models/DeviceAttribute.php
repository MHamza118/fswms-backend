<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceAttribute extends Model
{
    use HasFactory;


    protected $fillable = [
        'product_id',
        'condition',
        'model_number',
        'processor_type',
        'processor_speed',
        'processor_generation',
        'ram_size',
        'ram_type',
        'storage_size',
        'storage_type',
        'screen_size',
        'webcam',
        'touch_screen',
        'operating_system',
        'power_supply_unit',
        'pallet',
        'asset_sse',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
