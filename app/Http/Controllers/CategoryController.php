<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return successResponse("Categories retrieved successfully.", ['categories' => $categories]);
    }

    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'category_code' => 'required|unique:categories',
        'category_name' => 'required',
    ]);

    if ($validator->fails()) {
        return errorResponse("Validation failed",$validator->errors(),422);


    }

    $category = Category::create($validator->validated());

    return successResponse("Category created successfully.", ['Category' => $category]);

}


    /**
     * Display the specified resource.
     */
    public function show(Category $Category)
    {
        return $Category;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
{
    $validator = Validator::make($request->all(), [
        'category_code' => 'sometimes|required|string|max:255|unique:categories,category_code,' . $category->id,
        'category_name' => 'sometimes|required|string|max:255',
    ]);

    if ($validator->fails()) {
        return errorResponse("Validation failed",$validator->errors(),422);


    }

    // Update category with validated data
    $category->update($validator->validated());

    return successResponse("Category updated successfully.", ['category' => $category]);

}


    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(Category $category)
    // {
    //     $category->products()->delete(); // Delete all products associated with the brand
    //     //
    //     $category->delete();
    //     return successResponse("Category and associated products deleted successfully.", ['category' => $category]);
    // }
    public function destroy(Request $request, Category $category)
{
    // Step 1: Fetch associated products
    $associatedProducts = $category->products()->get();

    // Step 2: If confirmation not sent, return products and confirmation message
    if (!$request->has('confirm')) {
        return successResponse("The following products will be deleted with this category. Please confirm to proceed.", [
            'category' => $category,
            'products_to_be_deleted' => $associatedProducts,
            // 'confirm_url' => route('categories.destroy', ['category' => $category->id]) . '?confirm=1',
        ]);
    }

    // Step 3: Delete associated products
    $category->products()->delete();

    // Step 4: Delete the category itself
    $category->delete();

    return successResponse("Category and all associated products deleted successfully.", [
        'deleted_category' => $category,
        'deleted_products' => $associatedProducts,
    ]);
}

}
