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
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ItemRequestController;
use App\Http\Controllers\Admin\MarketingBannerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\InsuranceCompanyController;
use App\Http\Controllers\Admin\InsuranceRequestController;
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
        Route::get('/orders/check-new', [OrderController::class, 'checkNewOrders'])->name('orders.check-new');
        Route::get('/orders/new-count', [OrderController::class, 'getNewOrdersCount'])->name('orders.new-count');
        Route::get('/orders/delivery-persons', [OrderController::class, 'getDeliveryPersons'])->name('orders.delivery-persons');
        Route::post('/orders/bulk-assign-delivery', [OrderController::class, 'bulkAssignDelivery'])->name('orders.bulk-assign-delivery');
        Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/pack', [OrderController::class, 'pack'])->name('orders.pack');
        Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('/orders/{order}/mark-delivered', [OrderController::class, 'markDelivered'])->name('orders.mark-delivered');
        Route::post('/orders/{order}/mark-ready-pickup', [OrderController::class, 'markReadyPickup'])->name('orders.mark-ready-pickup');
        Route::post('/orders/{order}/mark-collected', [OrderController::class, 'markCollected'])->name('orders.mark-collected');
        Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        
        // Prescriptions
        Route::resource('prescriptions', PrescriptionController::class);
        Route::post('/prescriptions/{prescription}/approve', [PrescriptionController::class, 'approve'])->name('prescriptions.approve');
        Route::post('/prescriptions/{prescription}/reject', [PrescriptionController::class, 'reject'])->name('prescriptions.reject');
        Route::get('/prescriptions/{prescription}/download', [PrescriptionController::class, 'download'])->name('prescriptions.download');
        
        // Item Requests
        Route::resource('item-requests', ItemRequestController::class);
        Route::post('/item-requests/{itemRequest}/update-status', [ItemRequestController::class, 'updateStatus'])->name('item-requests.update-status');
        
        // Delivery Zones
        Route::resource('delivery-zones', DeliveryZoneController::class);
        
        // Users - Define edit/update routes explicitly with numeric constraint to take precedence over Filament
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])
            ->where('id', '[0-9]+')
            ->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])
            ->where('id', '[0-9]+')
            ->name('users.update');
        Route::patch('/users/{id}', [UserController::class, 'update'])
            ->where('id', '[0-9]+');
        Route::resource('users', UserController::class)->except(['edit', 'update']);
        
        // Banners
        Route::resource('banners', BannerController::class);
        
        // Marketing Banners
        Route::resource('marketing-banners', MarketingBannerController::class);
        
        // Pages
        Route::resource('pages', PageController::class);
        
        // Branches
        Route::resource('branches', BranchController::class);
        
        // Notifications
        Route::resource('notifications', NotificationController::class);
        
        // Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        // Profile
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        
        // Reports
        Route::get('/reports/profit-loss', [ReportController::class, 'profitLoss'])->name('reports.profit-loss');
        
        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
        
        // Insurance Companies
        Route::resource('insurance-companies', InsuranceCompanyController::class);
        
        // Insurance Requests
        Route::get('/insurance-requests', [InsuranceRequestController::class, 'index'])->name('insurance-requests.index');
        Route::get('/insurance-requests/export', [InsuranceRequestController::class, 'export'])->name('insurance-requests.export');
        Route::get('/insurance-requests/{insuranceRequest}', [InsuranceRequestController::class, 'show'])->name('insurance-requests.show');
        Route::post('/insurance-requests/{insuranceRequest}/approve', [InsuranceRequestController::class, 'approve'])->name('insurance-requests.approve');
        Route::post('/insurance-requests/{insuranceRequest}/reject', [InsuranceRequestController::class, 'reject'])->name('insurance-requests.reject');
        Route::post('/insurance-requests/{insuranceRequest}/create-order', [InsuranceRequestController::class, 'createOrder'])->name('insurance-requests.create-order');
    });
});

