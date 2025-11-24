<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([
                'status' => false,
                'message' => "Search query is required.",
                'data' => [],
            ], 422);
        }

        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('category_name', 'like', "%{$query}%");
            })
            ->orWhereHas('brand', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('unit', function ($q) use ($query) {
                $q->where('symbol', 'like', "%{$query}%");
            })
            ->with(['category', 'brand', 'unit'])
            ->get();

        if ($products->isEmpty()) {

            return successResponse("No products found matching the query");
        }

        return successResponse("Products retrieved successfully.", $products);
    }
}
