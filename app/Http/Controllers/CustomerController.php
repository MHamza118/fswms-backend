<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index()
    {
        return successResponse("Customers retrieved successfully.", Customer::all());
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:customers',
            'email' => 'nullable|email|unique:customers',
            'tax_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $customer = Customer::create($validator->validated());
        return successResponse("Customer created successfully.", ['customer' => $customer]);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        return successResponse("Customer retrieved successfully.", $customer);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20|unique:customers,phone,' . $customer->id,
            'email' => 'sometimes|nullable|email|unique:customers,email,' . $customer->id,
            'tax_number' => 'sometimes|nullable|string|max:50',
            'address' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $customer->update($validator->validated());
        return successResponse("Customer updated successfully.", ['customer' => $customer]);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return successResponse("Customer deleted successfully.", ['customer' => $customer]);
    }
}
