<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class BarcodeController extends Controller
{
    public function generateLabels(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'display_price' => 'boolean'
        ]);

        $displayPrice = $validated['display_price'] ?? false;

        $productIds = collect($validated['products'])->pluck('id');
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $labels = collect($validated['products'])->flatMap(function ($product) use ($products, $displayPrice) {
            $productData = $products[$product['id']] ?? null;

            if (!$productData) {
                return []; // Skip if product is not found (shouldn't happen due to validation)
            }

            return collect(range(1, $product['quantity']))->map(fn() => [
                'name' => $productData->name,
                'barcode' => $productData->barcode,
                'barcode_image' => generateBarcode($productData->barcode),
                'price' => $displayPrice ? $productData->selling_price : null
            ]);
        });

        return successResponse("Barcode generated successfully.", ['labels' => $labels]);
    }
}
