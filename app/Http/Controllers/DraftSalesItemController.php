<?php

namespace App\Http\Controllers;

use App\Models\DraftSalesItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DraftSalesItemController extends Controller
{
    /**
     * Display a listing of draft sales items.
     */
    public function index()
    {
        return successResponse("Draft Sales Items retrieved successfully.", DraftSalesItem::all());
    }

    /**
     * Store a newly created draft sales item.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $draftSalesItem = DraftSalesItem::create($validator->validated());
        return successResponse("Draft Sales Item created successfully.", ['draft_sales_item' => $draftSalesItem]);
    }

    /**
     * Display the specified draft sales item.
     */
    public function show(DraftSalesItem $draftSalesItem)
    {
        return successResponse("Draft Sales Item retrieved successfully.", $draftSalesItem);
    }

    /**
     * Update the specified draft sales item.
     */
    public function update(Request $request, DraftSalesItem $draftSalesItem)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'sometimes|required|numeric|min:0',
            'qty' => 'sometimes|required|integer|min:1',
            'subtotal' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $draftSalesItem->update($validator->validated());
        return successResponse("Draft Sales Item updated successfully.", ['draft_sales_item' => $draftSalesItem]);
    }

    /**
     * Remove the specified draft sales item.
     */
    public function destroy(DraftSalesItem $draftSalesItem)
    {
        $draftSalesItem->delete();
        return successResponse("Draft Sales Item deleted successfully.");
    }
}

