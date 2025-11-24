<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\DeviceAttribute;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Basic field validation
            if (!isset($row['sku'], $row['name'], $row['category'])) continue;

            // Fetch related models (case-insensitive)
            $category = Category::whereRaw('LOWER(category_name) = ?', [strtolower($row['category'])])->first();
            if (!$category) continue;

            $brand = Brand::whereRaw('LOWER(name) = ?', [strtolower($row['brand'])])->first();
            if (!$brand) continue;

            $warehouse = Warehouse::whereRaw('LOWER(name) = ?', [strtolower($row['warehouse'])])->first();
            if (!$warehouse) continue;

            $unit = Unit::whereRaw('LOWER(symbol) = ?', [strtolower($row['unit_symbol'])])->first();
            if (!$unit) continue;


            // Check for existing product based on SKU or barcode
            $existingProduct = Product::where('sku', $row['sku'])
                ->orWhere('barcode', $row['barcode'])
                ->first();

            if ($existingProduct) {
                // Skip if already exists
                continue;
            }

            // Prepare product data
            $productData = [
                'name' => $row['name'],
                'sku' => $row['sku'],
                'barcode' => $row['barcode'] ?? uniqid('barcode_'),
                'category_id' => $category->id,
                'unit_id' => $unit->id,
                'warehouse_id' => $warehouse->id,
                'brand_id' => $brand->id,
                'qty_alert' => $row['qty_alert'] ?? 0,
                'stock_quantity' => $row['stock_quantity'] ?? 0,
                'discount' => $row['discount'] ?? 0,
                'tax' => $row['tax'] ?? 0,
                'purchase_price' => $row['purchase_price'] ?? 0,
                'selling_price' => $row['selling_price'] ?? 0,
                'description' => $row['description'] ?? null,
            ];

            // Create new product
            $product = Product::create($productData);

            // Handle Device Attributes if category is 'laptop' or 'tablet'
            if (in_array(strtolower($row['category']), ['laptop', 'tablet'])) {
                DeviceAttribute::updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'condition' => $row['condition'] ?? null,
                        'model_number' => $row['model_number'] ?? null,
                        'processor_type' => $row['processor_type'] ?? null,
                        'processor_speed' => $row['processor_speed'] ?? null,
                        'processor_generation' => $row['processor_generation'] ?? null,
                        'ram_size' => $row['ram_size'] ?? null,
                        'ram_type' => $row['ram_type'] ?? null,
                        'storage_size' => $row['storage_size'] ?? null,
                        'storage_type' => $row['storage_type'] ?? null,
                        'screen_size' => $row['screen_size'] ?? null,
                        'webcam' => isset($row['webcam']) && $row['webcam'] == 'Y',
                        'touch_screen' => isset($row['touch_screen']) && $row['touch_screen'] == 'Y',
                        'operating_system' => $row['operating_system'] ?? null,
                        'power_supply_unit' => $row['power_supply_unit'] ?? null,
                        'pallet' => $row['pallet'] ?? null,
                        'asset_sse' => $row['asset_sse'] ?? null,
                    ]
                );
            }
        }
    }
}
