<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\DashboardController;

/*
|---------------------------------------------------------------------------
| Public Routes
|---------------------------------------------------------------------------
*/
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

/*
|---------------------------------------------------------------------------
| Authenticated Routes
|---------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function(){

    // User profile data (for all authenticated users)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Orders Routes - for managers and admins
    Route::get('/orders',[OrderController::class,'index']);
    Route::get('/orders/{id}',[OrderController::class,'show']);
});

/*
|---------------------------------------------------------------------------
| Admin Routes
|---------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum','role:manager|admin'])->group(function () {

    // Admin has full access to these resources
    Route::apiResource('products', ProductController::class);
    Route::apiResource('stocks', StockController::class); // Fixed route to StockController
    Route::apiResource('warehouses', WarehouseController::class);

});

/*
|---------------------------------------------------------------------------
| Manager Routes
|---------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum','role:manager|admin'])->group(function(){

    // Manager can access reports, dashboard, and create/order management
    Route::get('/dashboard', [DashboardController::class,'index']);

    // Reports routes
    Route::get('/reports/stock-summary',[ReportController::class,'stockSummary']);
    Route::get('/reports/sales',[ReportController::class,'salesReport']);
    Route::get('/reports/top-products',[ReportController::class,'topProducts']);

    // Manager can create and manage orders
    Route::post('/orders',[OrderController::class,'store']);
    Route::post('/orders/{id}/confirm',[OrderController::class,'confirm']);
    Route::post('/orders/{id}/cancel',[OrderController::class,'cancel']);

});

/*
|---------------------------------------------------------------------------
| Logout Route
|---------------------------------------------------------------------------
*/
Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:sanctum');

/*
|---------------------------------------------------------------------------
| Stock Management Routes (Admin Only)
|---------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum','role:admin'])->group(function(){

    // Admin can manage stock
    Route::get('/stocks',[StockController::class,'index']);
    Route::post('/stocks',[StockController::class,'store']);

});

Route::middleware('auth:sanctum')->get('/check', function () {
    return auth()->user();
});