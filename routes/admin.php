<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Authentication routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Categories
        Route::resource('categories', CategoryController::class);
        
        // Products
        Route::resource('products', ProductController::class);
        
        // Vendors
        Route::resource('vendors', VendorController::class);
        
        // Purchase Orders
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        
        // Orders
        Route::resource('orders', OrderController::class);
        Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/pack', [OrderController::class, 'pack'])->name('orders.pack');
        Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('/orders/{order}/mark-delivered', [OrderController::class, 'markDelivered'])->name('orders.mark-delivered');
        Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Prescriptions
        Route::resource('prescriptions', PrescriptionController::class);
        Route::post('/prescriptions/{prescription}/approve', [PrescriptionController::class, 'approve'])->name('prescriptions.approve');
        Route::post('/prescriptions/{prescription}/reject', [PrescriptionController::class, 'reject'])->name('prescriptions.reject');
        
        // Delivery Zones
        Route::resource('delivery-zones', DeliveryZoneController::class);
        
        // Users
        Route::resource('users', UserController::class);
    });
});

