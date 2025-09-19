<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/place_reservation', [CustomerController::class, 'place_reservation'])->name('customer.place_reservation');
Route::post('/customer/reserve', [CustomerController::class, 'storeReservation'])->name('customer.reserve');
Route::post('/customer/feedback', [CustomerController::class, 'storeFeedback'])->name('customer.feedback');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::get('/login/admin', [LoginController::class, 'adminLogin'])->name('admin.login');
    Route::post('/login/admin', [LoginController::class, 'adminLoginSubmit'])->name('admin.login.submit');

    Route::post('verify-code', [LoginController::class, 'verifyResetCode'])->name('admin.password.verify');
    Route::get('/login/admin/forgot-password', [LoginController::class, 'showAdminForgotPasswordForm'])->name('admin.password.request');
    Route::post('/login/admin/forgot-password', [LoginController::class, 'sendAdminResetLinkEmail'])->name('admin.password.email');
    Route::get('/login/admin/reset-password/{token}', [LoginController::class, 'showAdminResetForm'])->name('admin.password.reset');
    Route::post('/login/admin/reset-password', [LoginController::class, 'resetAdminPassword'])->name('admin.password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'home'])->name('home');

        Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
        Route::put('/updateprofile/{id}', [AdminController::class, 'updateProfile'])->name('updateprofile');
        Route::put('/changepassword/{id}', [AdminController::class, 'changePassword'])->name('changepassword');

        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/adduser', [AdminController::class, 'adduser'])->name('adduser');
        Route::post('/users/storeUser', [AdminController::class, 'storeUser'])->name('storeUser');
        Route::get('/edituser/{id}', [AdminController::class, 'edit'])->name('edituser');
        Route::put('/updateuser/{id}', [AdminController::class, 'update'])->name('updateuser');
        Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('destroyuser');
        Route::patch('/restoreuser/{id}', [AdminController::class, 'restore'])->name('restoreuser');
        Route::delete('/forcedeleteuser/{id}', [AdminController::class, 'forceDelete'])->name('forcedeleteuser');

        Route::get('/menu_management', [AdminController::class, 'menu_management'])->name('menu_management');
        Route::post('/addmenu', [AdminController::class, 'addmenu'])->name('addmenu');
        Route::post('/menu_management/storeMenu', [AdminController::class, 'storeMenu'])->name('storeMenu');
        Route::get('/editmenu/{id}', [AdminController::class, 'editMenu'])->name('editmenu');
        Route::put('/updatemenu/{id}', [AdminController::class, 'updateMenu'])->name('updatemenu');
        Route::delete('/deletemenu/{id}', [AdminController::class, 'deleteMenu'])->name('deleteMenu');
        Route::patch('/restoremenu/{id}', [AdminController::class, 'restoreMenu'])->name('restoreMenu');
        Route::delete('/forcedeletemenu/{id}', [AdminController::class, 'forceDeleteMenu'])->name('forceDeleteMenu');

        Route::get('/table_management', [AdminController::class, 'table_management'])->name('table_management');
        Route::post('/addtable', [AdminController::class, 'addtable'])->name('addtable');
        Route::post('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
        Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('edittable');
        Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('updatetable');
        Route::delete('/deletetable/{id}', [AdminController::class, 'deleteTable'])->name('deleteTable');
        Route::patch('/restoretable/{id}', [AdminController::class, 'restoreTable'])->name('restoreTable');
        Route::delete('/forcedeletetable/{id}', [AdminController::class, 'forceDeleteTable'])->name('forceDeleteTable');

        Route::get('/stock_management', [AdminController::class, 'stock_management'])->name('stock_management');
        Route::post('/addstock', [AdminController::class, 'addStock'])->name('addstock');
        Route::post('/stock_management/storeStock', [AdminController::class, 'storeStock'])->name('storeStock');
        Route::get('/editstock/{id}', [AdminController::class, 'editStock'])->name('editstock');
        Route::put('/updatestock/{id}', [AdminController::class, 'updateStock'])->name('updatestock');
        Route::delete('/deletestock/{id}', [AdminController::class, 'deleteStock'])->name('deleteStock');
        Route::patch('/restorestock/{id}', [AdminController::class, 'restoreStock'])->name('restoreStock');
        Route::delete('/forcedeletestock/{id}', [AdminController::class, 'forceDeleteStock'])->name('forceDeleteStock');

        Route::get('/ewallet', [AdminController::class, 'ewallet_management'])->name('ewallet_management');
        Route::post('/ewallet-store', [AdminController::class, 'ewallet_store'])->name('ewallet.store');
        Route::post('/ewallet/{id}/activate', [AdminController::class, 'activate'])->name('ewallet.activate');
        Route::post('/ewallet/{id}/deactivate', [AdminController::class, 'deactivate'])->name('ewallet.deactivate');

        Route::get('/feedback', [AdminController::class, 'feedback'])->name('feedback');

        Route::get('/reports', [ReportsController::class, 'index'])->name('reports');
        Route::get('/reports/sales', [ReportsController::class, 'salesReport'])->name('reports.sales');
        Route::get('/reports/revenue', [ReportsController::class, 'revenueReport'])->name('reports.revenue');
        Route::get('/reports/reservations', [ReportsController::class, 'reservationReport'])->name('reports.reservations');
        Route::get('/reports/staff', [ReportsController::class, 'staffReport'])->name('reports.staff');
        Route::get('/reports/stock', [ReportsController::class, 'stockReport'])->name('reports.stock');
        Route::get('/reports/export', [AdminController::class, 'export'])->name('reports.export');
        Route::get('/reports/export-csv', [AdminController::class, 'exportCsv'])->name('reports.export-csv');
    });

    Route::middleware('role:Receptionist')->prefix('receptionist')->name('receptionist.')->group(function () {
        Route::get('/dashboard', [ReceptionistController::class, 'home'])->name('home');
        Route::post('/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('storeReservation');
        Route::get('/view_reservations', [ReceptionistController::class, 'reservations'])->name('reservations');
        Route::get('/available-times', [ReceptionistController::class, 'getAvailableTimeSlots'])->name('available_times');
        Route::get('/modify_orders', [ReceptionistController::class, 'modifyOrders'])->name('modify_orders');
        Route::get('/view_kitchen', [ReceptionistController::class, 'viewKitchen'])->name('view_kitchen');
        Route::post('/update-order', [ReceptionistController::class, 'updateOrder'])->name('updateOrder');
        Route::post('/accept-reservation/{id}', [ReceptionistController::class, 'acceptReservation'])->name('accept-reservation');
        Route::get('/payments/{id}', [ReceptionistController::class, 'showPayment'])->name('showPayment');
        Route::get('/notifications', [ReceptionistController::class, 'getNotifications'])->name('notifications');
        Route::post('/cancel-reservation/{id}', [ReceptionistController::class, 'cancelReservation'])->name('cancel-reservation');
    });

    Route::middleware('role:Kitchen Staff')->prefix('kitchen')->name('kitchen.')->group(function () {
        Route::get('/dashboard', [KitchenController::class, 'home'])->name('home');
        Route::post('/update-stock', [KitchenController::class, 'updateStock'])->name('updateStock');
        Route::post('/complete-order', [KitchenController::class, 'storeCompletedOrders'])->name('completeOrder');
    });

    Route::middleware('role:Cashier')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/dashboard', [CashierController::class, 'home'])->name('home');
        Route::get('/orders/{reservationId}', [CashierController::class, 'getOrders']);
        Route::post('/process-payment', [CashierController::class, 'processPayment'])->name('process-payment');
        Route::get('/transaction-receipt/{transactionId}', [CashierController::class, 'getTransactionReceipt'])->name('transaction-receipt');
        Route::post('/accept-reservation/{id}', [CashierController::class, 'acceptReservation'])->name('accept-reservation');
        Route::get('/payments/{id}', [CashierController::class, 'showPayment'])->name('showPayment');
        Route::get('/notifications', [CashierController::class, 'getNotifications'])->name('notifications');
        Route::post('/cancel-reservation/{id}', [CashierController::class, 'cancelReservation'])->name('cancel-reservation');
        Route::get('/check-customer/{idNumber}', [CashierController::class, 'checkCustomer']);
        Route::post('/print-receipt', [CashierController::class, 'printReceipt']);
        Route::post('/test-printer', [CashierController::class, 'testPrinter']); // Optional for testing
    });
});

Route::fallback(function () {
    return redirect()->route('login');
});


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

