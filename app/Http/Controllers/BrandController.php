<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $brands = Brand::all();
        return $brands;

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);

        }

        $fields = $validator->validated();

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'brand_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('brands', $fileName, 'public');
            $fields['image'] = asset('storage/' . $filePath); // Store the image URL
        }

        $brand = Brand::create($fields);

        return successResponse("Brand created successfully.", ['brand' => $brand]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {

        return $brand;


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'sometimes|nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate new image
        ]);

        if ($validator->fails()) {
            return errorResponse("Validation failed", $validator->errors(), 422);
        }

        $fields = $validator->validated();

        // Handle image update if a new image is uploaded
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($brand->image) {
                $oldImagePath = str_replace(asset('storage/'), '', $brand->image);
                Storage::disk('public')->exists($oldImagePath);
                Storage::disk('public')->delete($oldImagePath);

            }

            // Upload new image
            $file = $request->file('image');
            $fileName = 'brand_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('brands', $fileName, 'public');
            $fields['image'] = asset('storage/' . $filePath); // Save new image URL
        }

        // Update the brand
        $brand->update($fields);

        return response()->json([
            'success' => true,
            'message' => 'Brand updated successfully',
            'data' => $brand,
        ], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Brand $brand)
    // {

    //     $brand->products()->delete(); // Delete associated products if any
    //     // Delete image if it exists
    //     if ($brand->image) {
    //         $oldImagePath = str_replace(asset('storage/'), '', $brand->image);
    //         Storage::disk('public')->delete($oldImagePath);
    //     }

    //     // Delete the brand
    //     $brand->delete();

    //     return successResponse("Brand and associated products deleted successfully.", ['brand' => $brand]);
    // }


    public function destroy(Request $request, Brand $brand)
{
    // Step 1: Fetch associated products
    $associatedProducts = $brand->products()->get();

    // Step 2: If confirmation not sent, return products and confirmation message
    if (!$request->has('confirm')) {
        return successResponse("The following products will be deleted with this brand. Please confirm to proceed.", [
            'brand' => $brand,
            'products_to_be_deleted' => $associatedProducts,
            // 'confirm_url' => route('brands.destroy', ['brand' => $brand->id]) . '?confirm=1',
        ]);
    }

    // Step 3: Delete associated products
    $brand->products()->delete();

    // Step 4: Delete brand image
    if ($brand->image) {
        $oldImagePath = str_replace(asset('storage/'), '', $brand->image);
        Storage::disk('public')->delete($oldImagePath);
    }

    // Step 5: Delete the brand itself
    $brand->delete();

    return successResponse("Brand and all associated products deleted successfully.", [
        'deleted_brand' => $brand,
        'deleted_products' => $associatedProducts,
    ]);
}


}
