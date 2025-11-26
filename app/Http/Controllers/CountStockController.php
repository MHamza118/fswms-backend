<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;

class CountStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function countStock($warehouse)
{
    $warehouseId = $warehouse;
    $warehouseModel = Warehouse::find($warehouseId);

    if (!$warehouseModel) {
        return errorResponse("Warehouse not found", [], 404);
    }

    $products = Product::where('warehouse_id', $warehouseId)
        ->with('unit:id,symbol')
        ->select('id', 'name', 'stock_quantity', 'sku', 'unit_id', 'warehouse_id')
        ->get();

    $stockData = $products->map(function ($product) {
        return [
            'product_id' => $product->id,
            'name' => $product->name,
            'stock_quantity' => $product->stock_quantity,
            'sku' => $product->sku,
            'symbol' => $product->unit ? $product->unit->symbol : null,
        ];
    });

    return successResponse("Stock data retrieved successfully.", [
        'warehouse_id' => $warehouseModel->id,
        'warehouse_name' => $warehouseModel->name,
        'products' => $stockData
    ]);
}







    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
