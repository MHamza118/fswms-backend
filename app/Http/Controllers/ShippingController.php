<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShippingController extends Controller
{
    public function index()
    {
        $shipment= Shipping::with(['sale', 'customer', 'warehouse'])->get();
        $formattedShipment=$shipment->map(function($shipments){
            return [
                'id'=>$shipments->id,
                'sale_id'=>$shipments->sale_id,
                'customer'=>$shipments->customer->name,
                'warehouse'=>$shipments->warehouse->name,
                'date_time' => $shipments->date_time,
                'status'=>$shipments->status,
                'deliver_to'=>$shipments->deliver_to,
                'address'=>$shipments->address,
                'description'=>$shipments->description,

            ];
        });
        return successResponse("Shipment retrieved successfully.", ['Shipments' => $formattedShipment]);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'status' => 'required|in:ordered,packed,shipped,delivered,cancelled',
            'deliver_to' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $shipping = Shipping::create($request->all());

        return successResponse("Shipment created successfully.", ['Shipment' => $shipping]);

    }

    public function show(Shipping $shipment)
    {
        $formattedShipment = [
            'id' => $shipment->id,
            'sale_id' => $shipment->sale_id,
            'customer' => $shipment->customer->name,
            'warehouse' => $shipment->warehouse->name,
            'date_time' => $shipment->date_time,
            'status' => $shipment->status,
            'deliver_to' => $shipment->deliver_to,
            'address' => $shipment->address,
            'description' => $shipment->description,
        ];

        return successResponse("Shipment retrieved successfully.", ['Shipment' => $formattedShipment]);
    }

    public function update(Request $request, Shipping $shipment)
    {
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required|exists:sales,id',
            'customer_id' => 'required|exists:customers,id',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'warehouse_id' => 'required|exists:warehouses,id',
            'status' => 'sometimes|required|in:pending,shipped,delivered,canceled',
            'deliver_to' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $shipment->update($request->all());

       return successResponse("Shipment retrieved successfully.", ['Shipment' => $shipment]);

    }

    public function destroy(Shipping $shipment)
    {
        $shipment->delete();

        return successResponse("Shipment deleted successfully.", ['Shipment' => $shipment], 200);

    }
}
