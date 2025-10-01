<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportsController;


Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/place_reservation', [CustomerController::class, 'place_reservation'])->name('customer.place_reservation');
Route::post('/customer/reserve', [CustomerController::class, 'storeReservation'])->name('customer.reserve');
Route::post('/customer/feedback', [CustomerController::class, 'storeFeedback'])->name('customer.feedback');
Route::get('/reservations/unavailable-times', [CustomerController::class, 'getUnavailableTimes'])->name('customer.unavailable-times');

Route::get('/file-serve/{path}', function ($path) {
    $possiblePaths = [
        storage_path('app/public/' . $path),
        public_path('storage/' . $path),
        base_path('storage/app/public/' . $path),
    ];
    foreach ($possiblePaths as $filePath) {
        if (file_exists($filePath) && is_readable($filePath)) {
            return response()->file($filePath);
        }
    }
    abort(404);
})->where('path', '.*');


Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    
    Route::get('/login/admin', [LoginController::class, 'adminLogin'])->name('admin.login');
    Route::post('/login/admin', [LoginController::class, 'adminLoginSubmit'])->name('admin.login.submit');
    
    Route::get('/login/admin/forgot-password', [LoginController::class, 'showAdminForgotPasswordForm'])->name('admin.password.request');
    Route::post('/login/admin/forgot-password', [LoginController::class, 'sendAdminResetLinkEmail'])->name('admin.password.email');
    Route::post('/verify-code', [LoginController::class, 'verifyResetCode'])->name('admin.password.verify');
    Route::get('/login/admin/reset-password/{token}', [LoginController::class, 'showAdminResetForm'])->name('admin.password.reset');
    Route::post('/login/admin/reset-password', [LoginController::class, 'resetAdminPassword'])->name('admin.password.update');
});


Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::post('/force-logout', [LoginController::class, 'forceLogout'])->name('force.logout');
    Route::get('/validate-session', [LoginController::class, 'validateSession'])->name('validate.session');

    // ADMIN  ROUTES
   Route::middleware('role:Admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'home'])->name('admin.home');
        Route::get('/home/dashboard-data', [AdminController::class, 'dashboardData'])->name('home.dashboard.data');
        Route::get('/home/sales-data', [AdminController::class, 'salesData'])->name('home.sales.data');
        Route::get('/debug/data', [AdminController::class, 'debugData'])->name('debug.data');
        
        // Profile
        Route::get('/myprofile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::put('/updateprofile/{id}', [AdminController::class, 'updateProfile'])->name('admin.updateprofile');
        Route::put('/changepassword/{id}', [AdminController::class, 'changePassword'])->name('admin.changepassword');
        
        // User Management
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/adduser', [AdminController::class, 'adduser'])->name('admin.adduser');
        Route::post('/users/storeUser', [AdminController::class, 'storeUser'])->name('storeUser');
        Route::get('/edituser/{id}', [AdminController::class, 'edit'])->name('admin.edituser');
        Route::put('/updateuser/{id}', [AdminController::class, 'update'])->name('admin.updateuser');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('admin.destroyuser');
        Route::patch('/restoreuser/{id}', [AdminController::class, 'restore'])->name('admin.restoreuser');
        Route::delete('/forcedeleteuser/{id}', [AdminController::class, 'forceDelete'])->name('admin.forcedeleteuser');
        
        // Menu Management
        Route::get('/menu_management', [AdminController::class, 'menu_management'])->name('admin.menu_management');
        Route::post('/addmenu', [AdminController::class, 'addmenu'])->name('admin.addmenu');
        Route::post('/menu_management/storeMenu', [AdminController::class, 'storeMenu'])->name('storeMenu');
        Route::get('/editmenu/{id}', [AdminController::class, 'editMenu'])->name('admin.editmenu');
        Route::put('/updatemenu/{id}', [AdminController::class, 'updateMenu'])->name('admin.updatemenu');
        Route::delete('/deletemenu/{id}', [AdminController::class, 'deleteMenu'])->name('admin.deleteMenu');
        Route::post('/deletemenu/{id}', [AdminController::class, 'deleteMenu']);
        Route::patch('/restoremenu/{id}', [AdminController::class, 'restoreMenu'])->name('admin.restoreMenu');
        Route::delete('/forcedeletemenu/{id}', [AdminController::class, 'forceDeleteMenu'])->name('admin.forceDeleteMenu');
        Route::get('/menu_ingredients', [AdminController::class, 'menuIngredients'])->name('admin.menu_ingredients');
        Route::post('/menu_ingredients/update', [AdminController::class, 'updateMenuIngredients'])->name('admin.menu_ingredients.update');
        Route::get('/menu/{id}/ingredients', [AdminController::class, 'getIngredients'])->name('menu.ingredients');
        Route::post('/menu/{menu}/add-ingredient', [AdminController::class, 'attachIngredient'])->name('menu.attachIngredient');
        Route::get('/ingredients/list', [AdminController::class, 'getAllIngredients'])->name('ingredients.list');
        Route::delete('/menu_ingredients/{id}', [AdminController::class, 'removeMenuIngredient'])->name('menu_ingredients.remove');
        
        // Table Management
        Route::get('/table_management', [AdminController::class, 'table_management'])->name('admin.table_management');
        Route::post('/addtable', [AdminController::class, 'addtable'])->name('admin.addtable');
        Route::post('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
        Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('admin.edittable');
        Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('admin.updatetable');
        Route::delete('/deletetable/{id}', [AdminController::class, 'deleteTable'])->name('admin.deleteTable');
        Route::patch('/restoretable/{id}', [AdminController::class, 'restoreTable'])->name('admin.restoreTable');
        Route::delete('/forcedeletetable/{id}', [AdminController::class, 'forceDeleteTable'])->name('admin.forceDeleteTable');
        
        // Ingredient Management
        Route::prefix('ingredient_management')->group(function () {
            Route::get('/', [AdminController::class, 'ingredient_management'])->name('admin.ingredient_management');
            Route::post('/storeIngredient', [AdminController::class, 'storeIngredient'])->name('storeIngredient');
            Route::post('/add-stock', [AdminController::class, 'addStock'])->name('ingredient.addStock');
            Route::post('/update-stock', [AdminController::class, 'updateStock'])->name('ingredient.updateStock');
            Route::get('/addStockForm', [AdminController::class, 'addStockForm'])->name('ingredient.addStockForm');
            Route::get('/updateStockForm', [AdminController::class, 'updateStockForm'])->name('ingredient.updateStockForm');
            Route::get('/stock-batches', [AdminController::class, 'getStockBatches'])->name('ingredient.stockBatches');
            Route::get('/expiry-data', [AdminController::class, 'expiryData'])->name('ingredients.expiry_data');
            Route::get('/expired-only', [AdminController::class, 'getExpiredOnly'])->name('ingredients.expired_only');
            Route::post('/remove-batch', [AdminController::class, 'removeBatch'])->name('ingredients.removeBatch');
            Route::get('/expired-history', [AdminController::class, 'getExpiredHistory'])->name('ingredients.expired_history');
        });
        
        // Analytics
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        
        // E-Wallet Management
        Route::get('/ewallet', [AdminController::class, 'ewallet_management'])->name('admin.ewallet_management');
        Route::post('/ewallet-store', [AdminController::class, 'ewallet_store'])->name('ewallet.store');
        Route::post('/ewallet/{id}/activate', [AdminController::class, 'activate'])->name('ewallet.activate');
        Route::post('/ewallet/{id}/deactivate', [AdminController::class, 'deactivate'])->name('ewallet.deactivate');
        
        // Feedback
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('admin.feedback');
        
        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
        Route::get('/reports/sales', [ReportsController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/revenue', [ReportsController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('/reports/reservations', [ReportsController::class, 'reservationReport'])->name('reports.reservations');
        Route::get('/reports/staff', [ReportsController::class, 'staffReport'])->name('reports.staff');
        Route::get('/reports/stock', [ReportsController::class, 'stockReport'])->name('reports.stock');
        Route::get('/reports/export', [AdminController::class, 'export'])->name('admin.reports.export');
        Route::get('/reports/export-csv', [AdminController::class, 'exportCsv'])->name('admin.reports.export-csv');
    });

    // RECEPTIONIST ONLY ROUTES
    Route::middleware('role:Receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
        Route::get('/home', [ReceptionistController::class, 'home'])->name('home');
        Route::get('/dashboard', [ReceptionistController::class, 'home']);
        Route::post('/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('storeReservation');
        Route::get('/available-times', [ReceptionistController::class, 'getAvailableTimeSlots'])->name('available_times');
        Route::get('/modify_orders', [ReceptionistController::class, 'modifyOrders'])->name('modify_orders');
        Route::get('/view_kitchen', [ReceptionistController::class, 'viewKitchen'])->name('view_kitchen');
        Route::post('/update-order', [ReceptionistController::class, 'updateOrder'])->name('updateOrder');
        Route::post('/accept-reservation/{id}', [ReceptionistController::class, 'acceptReservation'])->name('accept-reservation');
        Route::get('/payments/{id}', [ReceptionistController::class, 'showPayment'])->name('showPayment');
        Route::get('/notifications', [ReceptionistController::class, 'getNotifications'])->name('notifications');
        Route::post('/cancel-reservation/{id}', [ReceptionistController::class, 'cancelReservation'])->name('cancel-reservation');
        Route::post('/notifications/{id}/read', [ReceptionistController::class, 'markNotificationAsRead'])->name('notification.read');
        Route::get('/notifications/unread-count', [ReceptionistController::class, 'getUnreadCount'])->name('notification.unread-count');
        Route::post('/notifications/mark-all-read', [ReceptionistController::class, 'markAllNotificationsAsRead'])->name('notification.mark-all-read');
    });
    
    Route::middleware('role:Receptionist')->group(function () {
        Route::get('/view_reservations', [ReceptionistController::class, 'reservations'])->name('receptionist.reservations');
    });

    // KITCHEN STAFF ONLY ROUTES
    Route::middleware('role:Kitchen Staff')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/home', [KitchenController::class, 'home'])->name('home');
        Route::get('/dashboard', [KitchenController::class, 'home']);
        Route::post('/update-stock', [KitchenController::class, 'updateStock'])->name('updateStock');
        Route::post('/complete-order', [KitchenController::class, 'storeCompletedOrders'])->name('completeOrder');
    });

    // CASHIER ONLY ROUTES
    Route::middleware('role:Cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/home', [CashierController::class, 'home'])->name('home');
        Route::get('/dashboard', [CashierController::class, 'home']);
        Route::get('/payments/{id}', [CashierController::class, 'showPayment'])->name('showPayment');
        Route::post('/print-receipt', [CashierController::class, 'printReceipt']);
        Route::post('/test-printer', [CashierController::class, 'testPrinter']);
    });
    
    Route::middleware('role:Cashier')->group(function () {
        Route::get('/orders/{reservationId}', [CashierController::class, 'getOrders']);
        Route::post('/process-payment', [CashierController::class, 'processPayment'])->name('cashier.process-payment');
        Route::get('/transaction-receipt/{transactionId}', [CashierController::class, 'getTransactionReceipt'])->name('cashier.transaction-receipt');
        Route::get('/check-customer/{idNumber}', [CashierController::class, 'checkCustomer']);
    });

    // SHARED ROUTES (Multiple roles can access)
    Route::get('/api/menu-prices', function () {
        $menuItems = \App\Models\Menu::select([
            'menu_item',
            'regular_price',
            'student_price',
            'govt_employee_price',
            'has_customer_discount'
        ])->get();
        return response()->json($menuItems);
    });
});