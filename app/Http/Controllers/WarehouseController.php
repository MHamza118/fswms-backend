<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the warehouses.
     */
    public function index()
    {
        return successResponse("Warehouses retrieved successfully.", Warehouse::all());
    }

    /**
     * Store a newly created warehouse in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'email' => 'required|email|unique:warehouses', 
            'zip_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $warehouse = Warehouse::create($validator->validated());
        return successResponse("Warehouse created successfully.", ['warehouse' => $warehouse]);
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        return successResponse("Warehouse retrieved successfully.", $warehouse);
    }

    /**
     * Update the specified warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:warehouses,email,' . $warehouse->id,
            'zip_code' => 'sometimes|required|string|max:10',
            'address' => 'sometimes|required|string',
            'phone' => 'sometimes|required|string|max:20',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $warehouse->update($validator->validated());
        return successResponse("Warehouse updated successfully.", ['warehouse' => $warehouse]);
    }

    /**
     * Remove the specified warehouse from storage.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return successResponse("Warehouse deleted successfully.", ['warehouse' => $warehouse]);
    }
}
