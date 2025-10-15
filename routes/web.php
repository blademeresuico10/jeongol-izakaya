<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\WaitingStaffController;


Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/place_reservation', [CustomerController::class, 'place_reservation'])->name('customer.place_reservation');
Route::post('/customer/reserve', [CustomerController::class, 'storeReservation'])->name('customer.reserve');
Route::post('/customer/feedback', [CustomerController::class, 'storeFeedback'])->name('customer.feedback');
Route::get('/reservations/unavailable-times', [CustomerController::class, 'getUnavailableTimes'])->name('customer.unavailable-times');
Route::get('/customer/check-availability', [CustomerController::class, 'checkAvailability'])->name('customer.checkAvailability');

Route::get('/file-serve/{folder}/{filename}', function ($folder, $filename) {

    $allowedFolders = ['jeongol_menu', 'payment_proofs', 'profile_pictures'];
    if (!in_array($folder, $allowedFolders)) {
        abort(404, 'Folder not allowed.');
    }


    $filePath = storage_path("app/public/{$folder}/{$filename}");


    if (!file_exists($filePath) || !is_readable($filePath)) {
        abort(404, 'File not found.');
    }

    return Response::file($filePath);
});


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
        Route::patch('/restoremenu/{id}', [AdminController::class, 'restoreMenu'])->name('admin.restoreMenu');
        Route::delete('/forcedeletemenu/{id}', [AdminController::class, 'forceDeleteMenu'])->name('admin.forceDeleteMenu');
        Route::get('/menu_ingredients', [AdminController::class, 'menuIngredients'])->name('admin.menu_ingredients');
        Route::post('/menu_ingredients/update', [AdminController::class, 'updateMenuIngredients'])->name('admin.menu_ingredients.update');
        Route::get('/menu/{id}/ingredients', [AdminController::class, 'getIngredients'])->name('menu.ingredients');
        Route::post('/menu/{menu}/add-ingredient', [AdminController::class, 'attachIngredient'])->name('menu.attachIngredient');
        Route::get('/ingredients/list', [AdminController::class, 'getAllIngredients'])->name('ingredients.list');
        Route::delete('/menu_ingredients/{id}', [AdminController::class, 'removeMenuIngredient'])->name('menu_ingredients.remove');
        Route::get('/menu/{menuId}/existing-ingredients', [AdminController::class, 'getExistingIngredients'])->name('menu.existing-ingredients');
        Route::get('/menu/{menuId}/suggested-ingredients', [AdminController::class, 'getSuggestedIngredientsApi'])->name('menu.suggested-ingredients');
       
        // Table Management
        Route::get('/table_management', [AdminController::class, 'table_management'])->name('admin.table_management');
        Route::post('/addtable', [AdminController::class, 'addtable'])->name('admin.addtable');
        Route::post('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
        Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('admin.edittable');
        Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('admin.updatetable');
        Route::delete('/deletetable/{id}', [AdminController::class, 'deleteTable'])->name('admin.deleteTable');
        Route::patch('/restoretable/{id}', [AdminController::class, 'restoreTable'])->name('admin.restoreTable');
        Route::delete('/forcedeletetable/{id}', [AdminController::class, 'forceDeleteTable'])->name('admin.forceDeleteTable');
        Route::get('/table_management/tables', [AdminController::class, 'getTables'])->name('admin.getTables');

        // Ingredient Management
        Route::prefix('ingredient_management')->group(function () {
            Route::get('/', [AdminController::class, 'ingredient_management'])->name('admin.ingredient_management');
            Route::get('/stocks', [AdminController::class, 'getStocks'])->name('ingredient.stocks');
            Route::post('/storeIngredient', [AdminController::class, 'storeIngredient'])->name('storeIngredient');
            Route::post('/add-stock', [AdminController::class, 'addStock'])->name('ingredient.addStock');
            Route::post('/update-stock', [AdminController::class, 'updateStock'])->name('ingredient.updateStock');
            Route::get('/addStockForm', [AdminController::class, 'addStockForm'])->name('ingredient.addStockForm');
            Route::get('/updateStockForm', [AdminController::class, 'updateStockForm'])->name('ingredient.updateStockForm');
            Route::get('/stock-batches', [AdminController::class, 'getStockBatches'])->name('ingredient.stockBatches');
            Route::get('/expiry-data', [AdminController::class, 'expiryData'])->name('ingredients.expiry_data');
            Route::get('/expired-only', [AdminController::class, 'getExpiredOnly'])->name('ingredients.expired_only');
            Route::get('/expired-history', [AdminController::class, 'getExpiredHistory'])->name('ingredients.expired_history');
            Route::put('/batches/{id}', [AdminController::class, 'updateBatch'])->name('batches.update');
            Route::delete('/batches/delete', [AdminController::class, 'deleteBatch'])->name('batches.delete'); // Fixed path
        });

        // E-Wallet Management
        Route::get('/ewallet', [AdminController::class, 'ewallet_management'])->name('admin.ewallet_management');
        Route::post('/ewallet-store', [AdminController::class, 'ewallet_store'])->name('ewallet.store');
        Route::post('/ewallet/{id}/activate', [AdminController::class, 'activate'])->name('ewallet.activate');
        Route::post('/ewallet/{id}/deactivate', [AdminController::class, 'deactivate'])->name('ewallet.deactivate');

        // Others
        Route::get('/miscelanious', [AdminController::class, 'others'])->name('admin.others');
        // Add this to your routes/web.php inside the admin middleware group

        Route::put('/stock-levels/{id}', [AdminController::class, 'updateStockLevel'])->name('admin.stock_levels.update');

        // Operating Hours Management
        Route::put('/operating-hours/default', [AdminController::class, 'updateDefaultHours'])->name('admin.operating_hours.update_default');
        Route::post('/operating-hours', [AdminController::class, 'storeOperatingHours'])->name('admin.operating_hours.store');
        Route::put('/operating-hours/{id}', [AdminController::class, 'updateOperatingHours'])->name('admin.operating_hours.update');
        Route::delete('/operating-hours/{id}', [AdminController::class, 'deleteOperatingHours'])->name('admin.operating_hours.delete');

        // Discounts Management
        Route::post('/discounts', [AdminController::class, 'storeDiscount'])->name('admin.discounts.store');
        Route::put('/discounts/{id}', [AdminController::class, 'updateDiscount'])->name('admin.discounts.update');
        Route::delete('/discounts/{id}', [AdminController::class, 'deleteDiscount'])->name('admin.discounts.delete');

        // Feedback
        Route::get('/feedback', [AdminController::class, 'feedback'])->name('admin.feedback');

        // Reports
        Route::get('/reports', [ReportsController::class, 'index'])->name('admin.reports');
        Route::get('/reports/sales', [ReportsController::class, 'salesReportPdf'])->name('admin.sales_report.pdf');
        Route::get('/reports/transaction', [ReportsController::class, 'transactionReport'])->name('admin.transaction_reports');
        Route::get('/reports/stock', [ReportsController::class, 'stockReport'])->name('admin.stock_report');
        Route::get('/reports/export', [AdminController::class, 'export'])->name('admin.reports.export');
        Route::get('/reports/export-csv', [AdminController::class, 'exportCsv'])->name('admin.reports.export-csv');
    });

    // RECEPTIONIST ONLY ROUTES
    Route::middleware('role:Receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
        Route::get('/home', [ReceptionistController::class, 'home'])->name('home');
        Route::get('/dashboard', [ReceptionistController::class, 'home']);
        Route::post('/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('storeReservation');
        Route::post('/store-walkin', [ReceptionistController::class, 'storeWalkIn'])->name('storeWalkIn');
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
        Route::get('/view_reservations', [ReceptionistController::class, 'bookings'])->name('receptionist.bookings');
    });

    // KITCHEN STAFF ONLY ROUTES
    Route::middleware('role:Kitchen Staff')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/home', [KitchenController::class, 'home'])->name('home');
        Route::post('/served', [KitchenController::class, 'markAsServed'])->name('served');
        Route::post('/unlimited/refill', [KitchenController::class, 'storeUnlimitedRefill'])->name('unlimited.refill');
        Route::post('/orders/additional', [KitchenController::class, 'storeAdditionalOrder'])->name('orders.additional');
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

    Route::middleware('role:Wait Staff')->prefix('waitstaff')->name('waitstaff.')->group(function () {
        Route::get('/home', [WaitingStaffController::class, 'home'])->name('home');
    });

});
