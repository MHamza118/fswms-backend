<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class CountStockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function countStock(Warehouse $warehouse)
{
    $stockData = $warehouse->products()
        ->with('unit:id,symbol') // Load the unit relationship and fetch only id & name
        ->select('name', 'stock_quantity', 'sku', 'unit_id') // Include unit_id for relationship mapping
        ->get()
        ->map(function ($product) {
            return [
                'name' => $product->name,
                'stock_quantity' => $product->stock_quantity,
                'sku' => $product->sku,
                'symbol' => $product->unit ? $product->unit->symbol : null, // Get unit symbol if available
            ];
        });

    return successResponse("Stock data retrieved successfully.", [
        'warehouse_id' => $warehouse->id,
        'warehouse_name' => $warehouse->name,
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
