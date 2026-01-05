<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\InsuranceController;
use App\Http\Controllers\Web\PaystackPaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/categories', [ProductController::class, 'categories']);
Route::get('/banners', [BannerController::class, 'index']);
Route::get('/branches', [BranchController::class, 'index']);
Route::get('/branches/{id}', [BranchController::class, 'show']);
Route::get('/delivery-zones', [\App\Http\Controllers\Api\DeliveryZoneController::class, 'index']);
Route::get('/notifications', [NotificationController::class, 'index']);
// Note: /notifications/{id} moved to authenticated routes to avoid conflict with /notifications/user

// Settings
Route::get('/settings/currency', [\App\Http\Controllers\Api\SettingController::class, 'currency']);

// Insurance Companies (public - needed for form)
Route::get('/insurance-companies', [InsuranceController::class, 'getCompanies']);

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Payment routes (can be accessed by guests for checkout)
Route::prefix('paystack')->group(function () {
    Route::post('/initialize', [PaystackPaymentController::class, 'initialize']);
    Route::post('/verify', [PaystackPaymentController::class, 'verify']);
});

// Order creation (can be accessed by guests for checkout)
Route::post('/orders', [OrderController::class, 'store']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Cart
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/add', [CartController::class, 'add']);
        Route::put('/update/{id}', [CartController::class, 'update']);
        Route::delete('/remove/{id}', [CartController::class, 'remove']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });
    
    // Orders (authenticated routes - viewing orders requires auth)
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::get('/{id}/track', [OrderController::class, 'track']);
        Route::post('/{id}/cancel', [OrderController::class, 'cancel']);
    });
    
    // Prescriptions (can be accessed by guests)
    Route::prefix('prescriptions')->group(function () {
        Route::get('/', [PrescriptionController::class, 'index']);
        Route::post('/', [PrescriptionController::class, 'store']);
        Route::get('/{id}', [PrescriptionController::class, 'show']);
    });
    
    // Item Requests (can be accessed by guests)
    Route::prefix('item-requests')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ItemRequestController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\ItemRequestController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ItemRequestController::class, 'show']);
    });
    
    // Insurance Requests
    Route::prefix('insurance-requests')->group(function () {
        Route::get('/', [InsuranceController::class, 'index']);
        Route::post('/', [InsuranceController::class, 'store']);
        Route::get('/{id}', [InsuranceController::class, 'show']);
        Route::post('/{id}/create-order', [InsuranceController::class, 'createOrder']);
    });
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    
    // Addresses
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::get('/{id}', [AddressController::class, 'show']);
        Route::put('/{id}', [AddressController::class, 'update']);
        Route::delete('/{id}', [AddressController::class, 'destroy']);
        Route::post('/{id}/set-default', [AddressController::class, 'setDefault']);
    });
    
    // User Notifications
    Route::prefix('notifications')->group(function () {
        // Specific routes must come before parameterized routes
        Route::get('/user', [NotificationController::class, 'userNotifications']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/clear/all', [NotificationController::class, 'clearAll']);
        // Parameterized routes come after specific routes - only match numeric IDs
        Route::get('/{id}', [NotificationController::class, 'show'])->where('id', '[0-9]+');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->where('id', '[0-9]+');
        Route::delete('/{id}', [NotificationController::class, 'destroy'])->where('id', '[0-9]+');
    });
});
