<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query', '');
        $warehouseId = $request->input('warehouse_id', '');

        $products = Product::query();

        // Apply warehouse filter if provided
        if ($warehouseId) {
            $products->where('warehouse_id', $warehouseId);
        }

        // Apply search query if provided
        if ($query) {
            $products->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%")
                    ->orWhereHas('category', function ($subQ) use ($query) {
                        $subQ->where('category_name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('brand', function ($subQ) use ($query) {
                        $subQ->where('name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('unit', function ($subQ) use ($query) {
                        $subQ->where('symbol', 'like', "%{$query}%");
                    });
            });
        }

        $products = $products->with(['category', 'brand', 'unit', 'warehouse'])->get();

        if ($products->isEmpty()) {
            return successResponse("No products found", []);
        }

        return successResponse("Products retrieved successfully.", $products);
    }
}
