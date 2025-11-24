<?php
namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index()
    {
        return successResponse("Units retrieved successfully.", Unit::all());
    }

    /**
     * Store a newly created unit in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:units',
            'symbol' => 'required|string|max:10|unique:units',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $unit = Unit::create($validator->validated());
        return successResponse("Unit created successfully.", ['unit' => $unit]);
    }

    /**
     * Display the specified unit.
     */
    public function show(Unit $unit)
    {
        return successResponse("Unit retrieved successfully.", $unit);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:units,name,' . $unit->id,
            'symbol' => 'sometimes|required|string|max:10|unique:units,symbol,' . $unit->id,
            'description' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $unit->update($validator->validated());
        return successResponse("Unit updated successfully.", ['unit' => $unit]);
    }

    /**
     * Remove the specified unit from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return successResponse("Unit deleted successfully.", ['unit' => $unit]);
    }
}
