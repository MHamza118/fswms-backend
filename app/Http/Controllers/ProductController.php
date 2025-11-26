<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\DeviceAttribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get pagination parameter, default to 10 items per page
        $perPage = $request->input('per_page', 10);

        // Paginate products with relationships
        $paginatedProducts = Product::with(['category', 'unit', 'warehouse', 'brand', 'deviceAttribute'])
            ->paginate($perPage);

        // Modify each product to include deviceAttribute only for 'laptop' or 'tablet' category
        $products = $paginatedProducts->getCollection()->map(function ($product) {
            $categoryName = strtolower($product->category->category_name ?? '');

            if (in_array($categoryName, ['laptop', 'tablet'])) {
                $product->device_attribute = $product->deviceAttribute;
            } else {
                $product->device_attribute = null; // Or you can unset it
            }

            return $product;
        });

        // Set the modified collection back to paginator
        $paginatedProducts->setCollection($products);

        // Return response with products and pagination info
        return successResponse("Products retrieved successfully.", [
            'products' => $products,
            'pagination' => [
                'current_page' => $paginatedProducts->currentPage(),
                'last_page' => $paginatedProducts->lastPage(),
                'per_page' => $paginatedProducts->perPage(),
                'total' => $paginatedProducts->total(),
            ]
        ]);
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
        'name' => 'required|string|max:255',
        'sku' => 'required|string|unique:products',
        'barcode' => 'required|string|unique:products',
        'category_id' => 'required|exists:categories,id',
        'unit_id' => 'required|exists:units,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'brand_id' => 'required|exists:brands,id',
        'qty_alert' => 'required|integer|min:0',
        'stock_quantity' => 'sometimes|required|integer|min:0',
        'discount' => 'nullable|numeric|min:0',
        'tax' => 'nullable|numeric|min:0',
        'purchase_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
        'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        // Device-specific fields
        'condition' => 'nullable|string',
        'model_number' => 'nullable|string',
        'processor_type' => 'nullable|string',
        'processor_speed' => 'nullable|string',
        'processor_generation' => 'nullable|string',
        'ram_size' => 'nullable|string',
        'ram_type' => 'nullable|string',
        'storage_size' => 'nullable|string',
        'storage_type' => 'nullable|string',
        'screen_size' => 'nullable|string',
        'webcam' => 'nullable|boolean',
        'touch_screen' => 'nullable|boolean',
        'operating_system' => 'nullable|string',
        'power_supply_unit' => 'nullable|string',
        'pallet' => 'nullable|string',
        'asset_sse' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return errorResponse("Validation failed", $validator->errors(), 422);
    }

    $fields = $validator->validated();

    // Handle product_image upload
    if ($request->hasFile('product_image')) {
        $file = $request->file('product_image');
        $fileName = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('products', $fileName, 'public');
        $fields['product_image'] = asset('storage/' . $filePath);
    }

    // Create product
    $product = Product::create($fields);

    // Check category name
    $category = Category::find($request->category_id);
    if ($category && in_array(strtolower($category->category_name), ['laptop', 'tablet'])) {
        DeviceAttribute::create([
            'product_id' => $product->id,
            'condition' => $fields['condition'] ?? null,
            'model_number' => $fields['model_number'] ?? null,
            'processor_type' => $fields['processor_type'] ?? null,
            'processor_speed' => $fields['processor_speed'] ?? null,
            'processor_generation' => $fields['processor_generation'] ?? null,
            'ram_size' => $fields['ram_size'] ?? null,
            'ram_type' => $fields['ram_type'] ?? null,
            'storage_size' => $fields['storage_size'] ?? null,
            'storage_type' => $fields['storage_type'] ?? null,
            'screen_size' => $fields['screen_size'] ?? null,
            'webcam' => $fields['webcam'] ?? false,
            'touch_screen' => $fields['touch_screen'] ?? false,
            'operating_system' => $fields['operating_system'] ?? null,
            'power_supply_unit' => $fields['power_supply_unit'] ?? null,
            'pallet' => $fields['pallet'] ?? null,
            'asset_sse' => $fields['asset_sse'] ?? null,
        ]);
    }

    return successResponse("Product created successfully.", ['product' => $product]);
}


    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return successResponse("Product retrieved successfully.", $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, Product $product)
{
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
        'barcode' => 'sometimes|required|string|unique:products,barcode,' . $product->id,
        'category_id' => 'sometimes|required|exists:categories,id',
        'unit_id' => 'sometimes|required|exists:units,id',
        'stock_quantity' => 'sometimes|required|integer|min:0',
        'warehouse_id' => 'sometimes|required|exists:warehouses,id',
        'brand_id' => 'sometimes|required|exists:brands,id',
        'qty_alert' => 'sometimes|required|integer|min:0',
        'discount' => 'sometimes|nullable|numeric|min:0',
        'tax' => 'sometimes|nullable|numeric|min:0',
        'purchase_price' => 'sometimes|required|numeric|min:0',
        'selling_price' => 'sometimes|required|numeric|min:0',
        'description' => 'sometimes|nullable|string',
        'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        // Device attributes validation
        'condition' => 'nullable|string',
        'model_number' => 'nullable|string',
        'processor_type' => 'nullable|string',
        'processor_speed' => 'nullable|string',
        'processor_generation' => 'nullable|string',
        'ram_size' => 'nullable|string',
        'ram_type' => 'nullable|string',
        'storage_size' => 'nullable|string',
        'storage_type' => 'nullable|string',
        'screen_size' => 'nullable|string',
        'webcam' => 'nullable|string',
        'touch_screen' => 'nullable|string',
        'operating_system' => 'nullable|string',
        'power_supply_unit' => 'nullable|string',
        'pallet' => 'nullable|string',
        'asset_sse' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return errorResponse("Validation failed", $validator->errors(), 422);
    }

    $fields = $validator->validated();

    // Handle image update
    if ($request->hasFile('product_image')) {
        if ($product->product_image) {
            $oldImagePath = str_replace(asset('storage/'), '', $product->product_image);
            Storage::disk('public')->delete($oldImagePath);
        }

        $file = $request->file('product_image');
        $fileName = 'product_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('products', $fileName, 'public');
        $fields['product_image'] = asset('storage/' . $filePath);
    }

    // Update product
    $product->update($fields);

    // Check category name
    $category = $product->category; // assuming relation is set: Product::category()
    if (in_array(strtolower($category->category_name), ['laptop', 'tablet'])) {
        $deviceFields = [
            'condition' => $request->input('condition'),
            'model_number' => $request->input('model_number'),
            'processor_type' => $request->input('processor_type'),
            'processor_speed' => $request->input('processor_speed'),
            'processor_generation' => $request->input('processor_generation'),
            'ram_size' => $request->input('ram_size'),
            'ram_type' => $request->input('ram_type'),
            'storage_size' => $request->input('storage_size'),
            'storage_type' => $request->input('storage_type'),
            'screen_size' => $request->input('screen_size'),
            'webcam' => $request->input('webcam'),
            'touch_screen' => $request->input('touch_screen'),
            'operating_system' => $request->input('operating_system'),
            'power_supply_unit' => $request->input('power_supply_unit'),
            'pallet' => $request->input('pallet'),
            'asset_sse' => $request->input('asset_sse'),
            'product_id' => $product->id,
        ];

        // Update if exists, otherwise create
        DeviceAttribute::updateOrCreate(
            ['product_id' => $product->id],
            $deviceFields
        );
    }

    return successResponse("Product updated successfully.", ['product' => $product]);
}
    /**
     * Remove the specified resource from storage.
     */

     public function destroy(Product $product)
     {
         // Delete associated device attributes if exists
         $product->deviceAttribute()->delete();

         // Delete the image if it exists
         if ($product->product_image) {
             $imagePath = str_replace(asset('storage/'), '', $product->product_image);
             Storage::disk('public')->delete($imagePath);
         }

         $product->delete();

         return successResponse('Product deleted successfully', ['product' => $product]);
     }

}
