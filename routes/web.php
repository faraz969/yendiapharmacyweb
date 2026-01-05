<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\PaystackPaymentController;
use App\Http\Controllers\Web\OrderTrackingController;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterController;
use App\Http\Controllers\Web\User\DashboardController;
use App\Http\Controllers\Web\User\OrderController as UserOrderController;
use App\Http\Controllers\Web\User\ProfileController;
use App\Http\Controllers\Web\User\AddressController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Branch\BranchDashboardController;
use App\Http\Controllers\Delivery\DeliveryDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{id}', [ProductController::class, 'category'])->name('products.category');

// Pages (About, Privacy Policy, etc.)
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show');

// Order Tracking (Guest)
Route::get('/track-order', [OrderTrackingController::class, 'index'])->name('order.tracking.index');
Route::post('/track-order', [OrderTrackingController::class, 'track'])->name('order.tracking.track');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Paystack Payment Routes
Route::prefix('paystack')->name('paystack.')->group(function () {
    Route::post('/initialize', [PaystackPaymentController::class, 'initialize'])->name('initialize');
    Route::post('/verify', [PaystackPaymentController::class, 'verify'])->name('verify');
    Route::get('/callback', [PaystackPaymentController::class, 'callback'])->name('callback');
});

// Authenticated User Routes
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [UserOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [UserOrderController::class, 'show'])->name('orders.show');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Addresses
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Web\User\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Web\User\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Web\User\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [\App\Http\Controllers\Web\User\NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear/all', [\App\Http\Controllers\Web\User\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    
    // Services
    Route::prefix('services')->name('services.')->group(function () {
        Route::get('/insurance', [\App\Http\Controllers\Web\User\ServiceController::class, 'insurance'])->name('insurance');
        Route::post('/insurance', [\App\Http\Controllers\Web\User\ServiceController::class, 'storeInsurance'])->name('insurance.store');
        Route::get('/insurance-requests', [\App\Http\Controllers\Web\User\ServiceController::class, 'insuranceRequests'])->name('insurance-requests');
        Route::get('/insurance-requests/{id}', [\App\Http\Controllers\Web\User\ServiceController::class, 'showInsuranceRequest'])->name('insurance-requests.show');
        Route::get('/prescription', [\App\Http\Controllers\Web\User\ServiceController::class, 'prescription'])->name('prescription');
        Route::post('/prescription', [\App\Http\Controllers\Web\User\ServiceController::class, 'storePrescription'])->name('prescription.store');
        Route::get('/item-request', [\App\Http\Controllers\Web\User\ServiceController::class, 'itemRequest'])->name('item-request');
        Route::post('/item-request', [\App\Http\Controllers\Web\User\ServiceController::class, 'storeItemRequest'])->name('item-request.store');
    });
});

// Branch Dashboard Routes (for branch staff)
Route::middleware('auth')->prefix('branch')->name('branch.')->group(function () {
    Route::get('/dashboard', [BranchDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/{order}', [BranchDashboardController::class, 'show'])->name('orders.show');
    
    // Prescriptions
    Route::get('/prescriptions', [BranchDashboardController::class, 'prescriptions'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [BranchDashboardController::class, 'showPrescription'])->name('prescriptions.show');
    Route::post('/prescriptions/{prescription}/approve', [BranchDashboardController::class, 'approvePrescription'])->name('prescriptions.approve');
    Route::post('/prescriptions/{prescription}/reject', [BranchDashboardController::class, 'rejectPrescription'])->name('prescriptions.reject');
    
    // Item Requests
    Route::get('/item-requests', [BranchDashboardController::class, 'itemRequests'])->name('item-requests.index');
    Route::get('/item-requests/{itemRequest}', [BranchDashboardController::class, 'showItemRequest'])->name('item-requests.show');
    Route::post('/item-requests/{itemRequest}/update-status', [BranchDashboardController::class, 'updateItemRequestStatus'])->name('item-requests.update-status');
});

// Delivery Dashboard Routes (for delivery persons)
Route::middleware('auth')->prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/dashboard', [DeliveryDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders/{order}', [DeliveryDashboardController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/mark-delivered', [DeliveryDashboardController::class, 'markDelivered'])->name('orders.mark-delivered');
});
