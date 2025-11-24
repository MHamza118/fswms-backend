<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountStockController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DraftSaleController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProductSearchController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleItemController;
use App\Http\Controllers\SalesExportData;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SalesReportController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post("/users/login", [AuthController::class, 'login']);

// Protected routes
Route::middleware(["auth:sanctum"])->group(function () {
    Route::post("/users/logout", [AuthController::class, 'logout']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::post("users/register", [AuthController::class, "register"]);
    Route::apiResource("warehouses", WarehouseController::class);



});

// manager routes
Route::middleware(['auth:sanctum', 'isManager'])->group(function () {
    Route::apiResource("units", UnitController::class);


});

// POS routes (accessible to any authenticated user)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('sale-items', SaleItemController::class);
    Route::apiResource('draft-sales', DraftSaleController::class);
    Route::apiResource('shipment', ShippingController::class);
    Route::get("count-stock/{warehouse}", [CountStockController::class, 'countStock']);
    Route::post("barcode", [BarcodeController::class, 'generateLabels']);
    Route::apiResource('sales', SaleController::class);
    Route::post('import-products', [ProductImportController::class, 'import']);
    Route::get('/export', [ExportController::class, 'export']);




});

Route::apiResource("products", ProductController::class);
Route::get('/search/product/', [ProductSearchController::class, 'search']);
Route::apiResource("brands", BrandController::class);
Route::apiResource("categories", CategoryController::class);

// Dashboard / reports routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('sales-report')->group(function () {
        Route::get('total-paid', [SalesReportController::class, 'totalPaid']);
        Route::get('total-purchase', [SalesReportController::class, 'totalPurchase']);
        Route::get('net-profit', [SalesReportController::class, 'netProfit']);
        Route::get('year', [SalesReportController::class, 'yearlySales']);
        Route::get('month', [SalesReportController::class, 'monthlySales']);
        Route::get('monthly-sale', [SalesReportController::class, 'monthlySaleGraph']);
        Route::get('customer', [SalesReportController::class, 'topCustomers']);
        Route::get('monthly-stats', [SalesReportController::class, 'monthlyStats']);
        Route::get('lowstock', [SalesReportController::class, 'lowStock']);
    });
});
