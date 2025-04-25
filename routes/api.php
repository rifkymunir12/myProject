<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->group(function () {
    Route::apiResource('user', \App\Http\Controllers\UserController::class)->except('store');
    Route::apiResource('comment', \App\Http\Controllers\CommentController::class);
    Route::apiResource('item', \App\Http\Controllers\ItemController::class);
    Route::apiResource('item_unit', \App\Http\Controllers\ItemUnitController::class);
    Route::apiResource('invoice', \App\Http\Controllers\InvoiceController::class);
    Route::apiResource('post', \App\Http\Controllers\PostController::class);
    Route::apiResource('shipment', \App\Http\Controllers\ShipmentController::class);
    Route::apiResource('coupon', \App\Http\Controllers\CouponController::class);


    //ngeliat erp, ada fungsi index (kita buat untuk post aja) untuk update
    Route::post('payment_confirmation', [\App\Http\Controllers\PaymentController::class, 'send_payment_confirmation']);
    Route::post('update_status_payment', [\App\Http\Controllers\PaymentController::class, 'update_status_payment']);
    Route::post('cancel_purchase', [\App\Http\Controllers\PaymentController::class, 'cancel_purchase']);
    Route::post('update_item_amount', [\App\Http\Controllers\InventoryManagementController::class, 'update_item_amount']);
    Route::post('edit_item_stock', [\App\Http\Controllers\InventoryManagementController::class, 'edit_item_stock']);
    

    Route::post('add_admin_mod', [\App\Http\Controllers\AddAdminAndModController::class, 'add_admin_mod']);
    Route::get('print_invoice/{id}', [\App\Http\Controllers\InvoicePdfController::class, 'print_invoice']);
    Route::get('export-invoice', [\App\Http\Controllers\InvoiceExcelController::class, 'exportExcel']);
    Route::get('logout', [\App\Http\Controllers\LogoutController::class, 'logout']);

    Route::post('user_update', [\App\Http\Controllers\UpdateUserController::class, 'user_update']);
});

Route::middleware('guest:api')->group(function () {
    Route::post('register', [\App\Http\Controllers\RegisterController::class, 'register']);
});
