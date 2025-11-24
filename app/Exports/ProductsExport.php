<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromArray, WithHeadings
{
    protected $from;
    protected $to;
    // protected $category;

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
        // $this->category = $category;
    }

    public function array(): array
    {
        $query = Product::query()
            ->with(['category', 'brand', 'warehouse', 'unit', 'deviceAttribute']);

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [$this->from, $this->to]);
        }

        // if ($this->category) {
        //     $query->whereHas('category', function ($q) {
        //         $q->where('category_name', $this->category);
        //     });
        // }

        return $query->get()->map(function ($product) {
            $categoryName = strtolower($product->category->category_name ?? '');

            $baseData = [
                'ID' => $product->id,
                'Name' => $product->name,
                'SKU' => $product->sku,
                'Barcode' => $product->barcode,
                'Category' => $product->category->category_name ?? 'N/A',
                'Brand' => $product->brand->brand_name ?? 'N/A',
                'Warehouse' => $product->warehouse->name ?? 'N/A',
                'Unit' => $product->unit->unit_name ?? 'N/A',
                'Qty Alert' => $product->qty_alert,
                'Stock Quantity' => $product->stock_quantity,
                'Discount' => $product->discount,
                'Tax' => $product->tax,
                'Purchase Price' => $product->purchase_price,
                'Selling Price' => $product->selling_price,
            ];

            // Include device data if category is Laptop or Tablet
            if (in_array($categoryName, ['laptop', 'tablet'])) {
                $device = $product->deviceAttribute;

                $baseData += [
                    'Condition' => $device->condition ?? '',
                    'Model Number' => $device->model_number ?? '',
                    'Processor Type' => $device->processor_type ?? '',
                    'Processor Speed' => $device->processor_speed ?? '',
                    'Processor Generation' => $device->processor_generation ?? '',
                    'RAM Size' => $device->ram_size ?? '',
                    'RAM Type' => $device->ram_type ?? '',
                    'Storage Size' => $device->storage_size ?? '',
                    'Storage Type' => $device->storage_type ?? '',
                    'Screen Size' => $device->screen_size ?? '',
                    'Webcam' => $device->webcam ? 'Yes' : 'No',
                    'Touch Screen' => $device->touch_screen ? 'Yes' : 'No',
                    'Operating System' => $device->operating_system ?? '',
                    'Power Supply Unit' => $device->power_supply_unit ?? '',
                    'Pallet' => $device->pallet ?? '',
                    'Asset SSE' => $device->asset_sse ?? '',
                ];
            } else {
                // Leave device fields blank for non-laptop/tablet
                $baseData += [
                    'Condition' => '',
                    'Model Number' => '',
                    'Processor Type' => '',
                    'Processor Speed' => '',
                    'Processor Generation' => '',
                    'RAM Size' => '',
                    'RAM Type' => '',
                    'Storage Size' => '',
                    'Storage Type' => '',
                    'Screen Size' => '',
                    'Webcam' => '',
                    'Touch Screen' => '',
                    'Operating System' => '',
                    'Power Supply Unit' => '',
                    'Pallet' => '',
                    'Asset SSE' => '',
                ];
            }

            $baseData += [
                'Description' => $product->description ?? '',
                'Image URL' => $product->product_image ?? '',
                'Created At' => $product->created_at->format('Y-m-d H:i:s'),
            ];

            return $baseData;
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID', 'Name', 'SKU', 'Barcode', 'Category', 'Brand', 'Warehouse', 'Unit', 'Qty Alert',
            'Stock Quantity', 'Discount', 'Tax', 'Purchase Price', 'Selling Price',
            'Condition', 'Model Number', 'Processor Type', 'Processor Speed', 'Processor Generation',
            'RAM Size', 'RAM Type', 'Storage Size', 'Storage Type', 'Screen Size',
            'Webcam', 'Touch Screen', 'Operating System', 'Power Supply Unit', 'Pallet', 'Asset SSE',
            'Description', 'Image URL', 'Created At'
        ];
    }
}
