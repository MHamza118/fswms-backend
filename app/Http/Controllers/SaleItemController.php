<?php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SaleItemController extends Controller
{
    /**
     * Display a listing of the sale items.
     */
    public function index()
    {
        return successResponse("Sale items retrieved successfully.", SaleItem::all());
    }

    /**
     * Store a newly created sale item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $saleItem = SaleItem::create($validator->validated());
        return successResponse("Sale item created successfully.", ['saleItem' => $saleItem]);
    }

    /**
     * Display the specified sale item.
     */
    public function show(SaleItem $saleItem)
    {
        return successResponse("Sale item retrieved successfully.", $saleItem);
    }

    /**
     * Update the specified sale item.
     */
    public function update(Request $request, SaleItem $saleItem)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'sometimes|required|exists:sales,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'price' => 'sometimes|required|numeric|min:0',
            'qty' => 'sometimes|required|integer|min:1',
            'subtotal' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $saleItem->update($validator->validated());
        return successResponse("Sale item updated successfully.", ['saleItem' => $saleItem]);
    }

    /**
     * Remove the specified sale item.
     */
    public function destroy(SaleItem $saleItem)
    {
        $saleItem->delete();
        return successResponse("Sale item deleted successfully.");
    }
}
