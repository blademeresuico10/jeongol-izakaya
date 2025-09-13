<?php

use App\Http\Controllers\CashierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;
use App\Models\reservation;
use App\Notifications\ReservationPaid;

// Customer landing page
Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/place_reservation', [CustomerController::class, 'place_reservation'])->name('customer.place_reservation');
Route::post('/customer/reserve', [CustomerController::class, 'storeReservation'])->name('customer.reserve');
Route::post('/customer/feedback', [CustomerController::class, 'storeFeedback'])->name('customer.feedback');

// Login routes
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

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

Route::middleware(['auth'])->group(function () {

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

    // Admin Routes
    Route::get('/home', [AdminController::class, 'index'])->name('admin.home');
    Route::get('/home/dashboard-data', [AdminController::class, 'dashboardData'])->name('home.dashboard.data');
    Route::get('/home/sales-data', [AdminController::class, 'salesData'])->name('home.sales.data');
    Route::get('/debug/data', [AdminController::class, 'debugData'])->name('debug.data');

    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/adduser', [AdminController::class, 'adduser'])->name('admin.adduser');
    Route::post('/users/storeUser', [AdminController::class, 'storeUser'])->name('storeUser');
    Route::get('/edituser/{id}', [AdminController::class, 'edit'])->name('admin.edituser');
    Route::put('/updateuser/{id}', [AdminController::class, 'update'])->name('admin.updateuser');

    // Admin menu management
    Route::get('/menu_management', [AdminController::class, 'menu_management'])->name('admin.menu_management');
    Route::post('/addmenu', [AdminController::class, 'addmenu'])->name('admin.addmenu');
    Route::post('/menu_management/storeMenu', [AdminController::class, 'storeMenu'])->name('storeMenu');
    Route::get('/editmenu/{id}', [AdminController::class, 'editMenu'])->name('admin.editmenu');
    Route::put('/updatemenu/{id}', [AdminController::class, 'updateMenu'])->name('admin.updatemenu');
    Route::delete('/deletemenu/{id}', [AdminController::class, 'deleteMenu'])->name('admin.deleteMenu');
    Route::post('/deletemenu/{id}', [AdminController::class, 'deleteMenu']);
    Route::patch('/restoremenu/{id}', [AdminController::class, 'restoreMenu'])->name('admin.restoreMenu');
    Route::delete('/forcedeletemenu/{id}', [AdminController::class, 'forceDeleteMenu'])->name('admin.forceDeleteMenu');

    // Admin table management
    Route::get('/table_management', [AdminController::class, 'table_management'])->name('admin.table_management');
    Route::post('/addtable', [AdminController::class, 'addtable'])->name('admin.addtable');
    Route::post('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
    Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('admin.edittable');
    Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('admin.updatetable');
    Route::delete('/deletetable/{id}', [AdminController::class, 'deleteTable'])->name('admin.deleteTable');
    Route::patch('/restoretable/{id}', [AdminController::class, 'restoreTable'])->name('admin.restoreTable');
    Route::delete('/forcedeletetable/{id}', [AdminController::class, 'forceDeleteTable'])->name('admin.forceDeleteTable');

    // Admin stock management
    Route::get('/stock_management', [AdminController::class, 'stock_management'])->name('admin.stock_management');
    Route::post('/addstock', [AdminController::class, 'addStock'])->name('admin.addstock');
    Route::post('/stock_management/storeStock', [AdminController::class, 'storeStock'])->name('admin.storeStock');
    Route::get('/editstock/{id}', [AdminController::class, 'editStock'])->name('admin.editstock');
    Route::put('/updatestock/{id}', [AdminController::class, 'updateStock'])->name('admin.updateStock');
    Route::delete('/deletestock/{id}', [AdminController::class, 'deleteStock'])->name('admin.deleteStock');
    Route::patch('/restorestock/{id}', [AdminController::class, 'restoreStock'])->name('admin.restoreStock');
    Route::delete('/forcedeletestock/{id}', [AdminController::class, 'forceDeleteStock'])->name('admin.forceDeleteStock');

    //Admin e-wallet management
    Route::get('/ewallet', [AdminController::class, 'ewallet_management'])->name('admin.ewallet_management');
    Route::post('/ewallet-store', [AdminController::class, 'ewallet_store'])->name('ewallet.store');
    Route::post('/ewallet/{id}/activate', [AdminController::class, 'activate'])->name('ewallet.activate');
    Route::post('/ewallet/{id}/deactivate', [AdminController::class, 'deactivate'])->name('ewallet.deactivate');

    // Admin feedback view
    Route::get('/feedback', [AdminController::class, 'feedback'])->name('admin.feedback');


    // Reports view
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');

    // Export routes
    Route::get('/reports/export', [AdminController::class, 'export'])->name('admin.reports.export');
    Route::get('/reports/export-csv', [AdminController::class, 'exportCsv'])->name('admin.reports.export-csv');

    // Receptionist Routes
    Route::get('/receptionist/home', [ReceptionistController::class, 'home'])->name('receptionist.home');
    Route::post('/receptionist/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('receptionist.storeReservation');
    Route::get('/view_reservations', [ReceptionistController::class, 'reservations'])->name('receptionist.reservations');
    Route::get('/receptionist/available-times', [ReceptionistController::class, 'getAvailableTimeSlots'])->name('receptionist.available_times');
    Route::get('/receptionist/modify_orders', [ReceptionistController::class, 'modifyOrders'])->name('receptionist.modify_orders');
    Route::get('/receptionist/view_kitchen', [ReceptionistController::class, 'viewKitchen'])->name('receptionist.view_kitchen');
    Route::post('/receptionist/update-order', [ReceptionistController::class, 'updateOrder'])->name('receptionist.updateOrder');
    Route::post('/receptionist/accept-reservation/{id}', [ReceptionistController::class, 'acceptReservation'])->name('receptionist.accept-reservation');
    Route::get('/receptionist/payments/{id}', [ReceptionistController::class, 'showPayment'])->name('receptionist.showPayment');
    Route::get('/receptionist/notifications', [ReceptionistController::class, 'getNotifications'])->name('receptionist.notifications');
    Route::post('/receptionist/cancel-reservation/{id}', [ReceptionistController::class, 'cancelReservation'])->name('receptionist.cancel-reservation');

    // Kitchen Routes
    Route::get('/kitchen/home', [KitchenController::class, 'home'])->name('kitchen.home');
    Route::post('/kitchen/update-stock', [KitchenController::class, 'updateStock'])->name('kitchen.updateStock');
    Route::post('/kitchen/complete-order', [KitchenController::class, 'storeCompletedOrders'])->name('kitchen.completeOrder');

    // Cashier Routes
    Route::get('/cashier/home', [CashierController::class, 'home'])->name('cashier.home');
    Route::get('/orders/{reservationId}', [CashierController::class, 'getOrders']);
    Route::post('/process-payment', [CashierController::class, 'processPayment'])->name('cashier.process-payment');
    Route::get('/transaction-receipt/{transactionId}', [CashierController::class, 'getTransactionReceipt'])->name('cashier.transaction-receipt');
    Route::post('/cashier/accept-reservation/{id}', [CashierController::class, 'acceptReservation'])->name('cashier.accept-reservation');
    Route::get('/cashier/payments/{id}', [CashierController::class, 'showPayment'])->name('cashier.showPayment');
    Route::get('/cashier/notifications', [CashierController::class, 'getNotifications'])->name('cashier.notifications');
    Route::post('/cashier/cancel-reservation/{id}', [CashierController::class, 'cancelReservation'])->name('cashier.cancel-reservation');
    Route::get('/check-customer/{idNumber}', [CashierController::class, 'checkCustomer']);
    Route::post('/print-receipt', [CashierController::class, 'printReceipt']);
    Route::post('/test-printer', [CashierController::class, 'testPrinter']); // Optional for testing
});
