<?php

namespace App\Http\Controllers;

use App\Models\DraftSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DraftSaleController extends Controller
{
    /**
     * Display a listing of draft sales.
     */
    public function index()
    {
        return successResponse("Draft Sales retrieved successfully.", DraftSale::with(['customer', 'warehouse', 'user', 'draftSaleItems'])
    ->get()
    ->map(function ($sale) {
        $total = $sale->draftSaleItems->sum('subtotal'); // Calculate grand total from subtotals

        return [
            'customer_name' => $sale->customer->name ?? 'N/A',
            'warehouse_name' => $sale->warehouse->name ?? 'N/A',
            'user_name' => $sale->user->name ?? 'N/A',
            'grand_total' => number_format($total, 2), // Format the grand total
            'products' => $sale->draftSaleItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'price' => $item->price,
                    'quantity' => $item->qty,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ];
    })
);
  }

    /**
     * Store a newly created draft sale.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            // 'grand_total' => 'required|numeric|min:0',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,completed,canceled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        // Extract only the DraftSale fields
        $draftSaleData = $request->only(['customer_id', 'warehouse_id',  'date', 'user_id', 'status']);

        // Create draft sale
        $draftSale = DraftSale::create($draftSaleData);

        // Store items
        foreach ($request->items as $item) {
            $draftSale->draftSaleItems()->create($item);
        }

        return successResponse("Draft Sale created successfully.", ['draft_sale' => $draftSale->load('draftSaleItems')]);
    }

    /**
     * Display the specified draft sale.
     */
    public function show(DraftSale $draftSale)
    {
        return successResponse("Draft Sale retrieved successfully.", $draftSale->load(['customer', 'warehouse', 'user', 'draftSaleItems']));
    }

    /**
     * Update the specified draft sale.
     */
    public function update(Request $request, DraftSale $draftSale)
    {
        $validator = Validator::make($request->all(), [
            // 'grand_total' => 'sometimes|required|numeric|min:0',
            'status' => 'sometimes|required|in:pending,completed,canceled',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $draftSale->update($validator->validated());

        return successResponse("Draft Sale updated successfully.", ['draft_sale' => $draftSale]);
    }

    /**
     * Remove the specified draft sale.
     */
    public function destroy(DraftSale $draftSale)
    {
        $draftSale->draftSaleItems()->delete();
        $draftSale->delete();

        return successResponse("Draft Sale deleted successfully.");
    }
}
