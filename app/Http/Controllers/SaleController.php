<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Shipping;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {
    $sales = Sale::with(['saleItems.product', 'customer', 'warehouse', 'user'])->get();

    $formattedSales = $sales->map(function ($sale) {
        return [
            'date' => $sale->created_at->format('Y-m-d'),
            'reference' => $sale->id,
            'added_by' => $sale->user->name ?? 'N/A',
            'customer' => $sale->customer->name ?? 'N/A',
            'warehouse' => $sale->warehouse->name ?? 'N/A',
            'status' => $sale->status ?? 'Pending',
            'grand_total' => $sale->grand_total,
            'paid' => $sale->paid,
            'due' => $sale->due,
            'tax' => $sale->tax,
            'discount' => $sale->discount,
            'shipping_cost' => $sale->shipping_cost,
            'payment_status' => $sale->payment_status ?? 'Unpaid',
            'shipping_status' => $sale->shipping_status ?? 'Pending',
            'currency' => $sale->currency ?? 'N/A',
            'expected_delivery_date' => $sale->expected_delivery_date ?? null,
            'payment_method' => $sale->payment_method ?? 'N/A',

            // Retrieving items belonging to the sale
            'saleItems' => $sale->saleItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'sale_id' => $item->sale_id,
                    'product_name' => $item->product->name ?? 'N/A',
                    'product_id' => $item->product_id,
                    'price' => $item->price,
                    'qty' => $item->qty,
                    'subtotal' => $item->subtotal,
                ];
            }),
        ];
    });

    return successResponse("Sales retrieved successfully.", ['Sales' => $formattedSales]);
}


public function show(Sale $sale)
{
    $formattedSale = [
        'date' => $sale->created_at->format('Y-m-d'),
        'reference' => $sale->id,
        'added_by' => $sale->user->name ?? 'N/A',
        'customer' => $sale->customer->name ?? 'N/A',
        'warehouse' => $sale->warehouse->name ?? 'N/A',
        'status' => $sale->status ?? 'Pending',
        'grand_total' => $sale->grand_total,
        'paid' => $sale->paid,
        'due' => $sale->due,
        'tax' => $sale->tax,
        'discount' => $sale->discount,
        'shipping_cost' => $sale->shipping_cost,
        'payment_status' => $sale->payment_status ?? 'Unpaid',
        'shipping_status' => $sale->shipping_status ?? 'Pending',
        'currency' => $sale->currency ?? 'N/A',
        'expected_delivery_date' => $sale->expected_delivery_date ?? null,
        'payment_method' => $sale->payment_method ?? 'N/A',

        // Retrieving items belonging to the sale
        'saleItems' => $sale->saleItems->map(function ($item) {
            return [
                'id' => $item->id,
                'sale_id' => $item->sale_id,
                'product_name' => $item->product->name ?? 'N/A',
                'product_id' => $item->product_id,
                'price' => $item->price,
                'qty' => $item->qty,
                'subtotal' => $item->subtotal,
            ];
        }),
    ];

    return successResponse("Sale retrieved successfully.", ['Sale' => $formattedSale]);
}



    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                // 'user_id' => 'required|exists:users,id',
                'warehouse_id' => 'required|exists:warehouses,id',
                'tax' => 'required|numeric',
                'discount' => 'required|numeric',
                'shipping_cost' => 'required|numeric',
                'grand_total' => 'required|numeric',
                'paid' => 'required|numeric',
                'due' => 'required|numeric',
                'description' => 'nullable|string',
                'status' => 'required|in:pending,processing,completed,canceled',
                'payment_status' => 'required|in:pending,paid,failed',
                'currency' => 'required|string',
                'expected_delivery_date' => 'nullable|date',
                'payment_method' => 'required|string',
                'saleItems' => 'required|array|min:1',
                'saleItems.*.product_id' => 'required|exists:products,id',
                'saleItems.*.price' => 'required|numeric',
                'saleItems.*.qty' => 'required|integer|min:1',
                'saleItems.*.subtotal' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return errorResponse("Validation failed", $validator->errors(), 422);
            }

             // Add the current user ID to the request
            $request->merge(['user_id' => auth()->id()]);

            DB::beginTransaction(); // Start transaction

            $sale = Sale::create($request->only([
                'customer_id', 'user_id', 'warehouse_id', 'tax', 'discount', 'shipping_cost', 'grand_total', 'paid', 'due', 'description', 'status', 'payment_status', 'currency', 'expected_delivery_date', 'payment_method'
            ]));
            // Manually merge 'sale_id' with the request data
             $shipmentData = $request->only(['customer_id', 'warehouse_id']);
            $shipmentData['sale_id'] = $sale->id;
            $shipmentData['date_time'] = now();

            $shipment = Shipping::create($shipmentData);

            foreach ($request->saleItems as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock_quantity < $item['qty']) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock for product: ' . $product->name,
                        'status_code' => 400
                    ], 400);
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'qty' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                $product->decrement('stock_quantity', $item['qty']);
            }

            DB::commit();
            return successResponse("Sale created successfully.", ['unit' => $sale->load('saleItems')]);


        } catch (\Exception $e) {
            DB::rollBack();
    return errorResponse("Something went wrong", ['Error' => $e->getMessage()],500);

        }
    }

    public function update(Request $request, Sale $sale)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                // 'user_id' => 'required|exists:users,id',
                'status' => 'required|in:pending,processing,completed,canceled',
                'payment_status' => 'required|in:pending,paid,failed',
                'warehouse_id' => 'required|exists:warehouses,id',
                'tax' => 'required|numeric',
                'discount' => 'required|numeric',
                'shipping_cost' => 'required|numeric',
                'grand_total' => 'required|numeric',
                'paid' => 'required|numeric',
                'due' => 'required|numeric',
                'description' => 'nullable|string',
                'currency' => 'required|string',
                'expected_delivery_date' => 'nullable|date',
                'payment_method' => 'required|string',
                'saleItems' => 'nullable|array',
                'saleItems.*.id' => 'nullable|exists:sale_items,id',
                'saleItems.*.product_id' => 'required|exists:products,id',
                'saleItems.*.price' => 'required|numeric',
                'saleItems.*.qty' => 'required|integer',
                'saleItems.*.subtotal' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return errorResponse("Validation failed", $validator->errors(), 422);
            }

             // Add the current user ID to the request
             $request->merge(['user_id' => auth()->id()]);


            DB::beginTransaction();

            $sale->update($request->only([
                'customer_id', 'user_id', 'warehouse_id', 'tax', 'discount', 'shipping_cost',
                'grand_total', 'paid', 'due', 'description', 'status', 'payment_status',
                'currency', 'expected_delivery_date', 'payment_method'
            ]));

            // Handle Sale Items
            $existingItems = $sale->saleItems->pluck('id')->toArray();
            $requestItemIds = collect($request->saleItems)->pluck('id')->filter()->toArray();
            $itemsToDelete = array_diff($existingItems, $requestItemIds);
            SaleItem::destroy($itemsToDelete);

            foreach ($request->saleItems as $item) {
                if (isset($item['id']) && in_array($item['id'], $existingItems)) {
                    SaleItem::where('id', $item['id'])->update([
                        'product_id' => $item['product_id'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                } else {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'price' => $item['price'],
                        'qty' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }

            DB::commit();
            return successResponse("Sale updated successfully.", ['sale' => $sale->load('saleItems')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return errorResponse("Something went wrong", ['Error' => $e->getMessage()], 500);
        }
    }


    public function destroy(Sale $sale)
{
    try {
        DB::beginTransaction();

        $sale->saleItems()->delete(); // Delete related sale items
        $sale->delete(); // Delete the sale itself

        DB::commit();
        return successResponse("Sale deleted successfully.", ['sale' => $sale], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return errorResponse("Something went wrong", ['Error' => $e->getMessage()], 500);
    }
}

}
