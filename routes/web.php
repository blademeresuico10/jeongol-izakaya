<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\CustomerController;

// Login 
Route::get('/', [LoginController::class, 'index']);
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');


// Admin 
Route::get('/home', [AdminController::class, 'index'])->name('admin.home');
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
//Admin table management
Route::get('/table_management', [AdminController::class, 'table_management'])->name('admin.table_management');
Route::post('/addtable', [AdminController::class, 'addtable'])->name('admin.addtable');
Route::post('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('admin.edittable');
Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('admin.updatetable');
// Admin stock management
Route::get('/stock_management', [AdminController::class, 'stock_management'])->name('admin.stock_management');
Route::get('/addstock', [AdminController::class, 'addStock'])->name('admin.addstock');
Route::post('/stock_management/storeStock', [AdminController::class, 'storeStock'])->name('admin.storeStock');
Route::put('/updatestock/{id}', [AdminController::class, 'updateStock'])->name('admin.updateStock');
Route::get('/editstock/{id}', [AdminController::class, 'editStock'])->name('admin.editstock');

Route::get('/reports', function () {
    return view('admin.reports');
});


// Receptionist 
Route::middleware(['auth'])->group(function () {
    Route::get('/receptionist/home', [ReceptionistController::class, 'home'])->name('receptionist.home');
    Route::post('/receptionist/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('receptionist.storeReservation');
    Route::get('/view_reservations', [ReceptionistController::class, 'reservations'])->name('receptionist.reservations');
    Route::get('/receptionist/available-times', [ReceptionistController::class, 'getAvailableTimeSlots']);


});


//Kitchen
Route::get('/kitchen/home', [KitchenController::class, 'home'])->name('kitchen.home');
Route::post('/kitchen/update-stock', [KitchenController::class, 'updateStock'])->name('kitchen.updateStock');


//Customer
Route::get('/customer/index', [CustomerController::class, 'index'])->name('customer.index');
Route::get('/customer/place_reservation', [CustomerController::class, 'place_reservation'])->name('customer.place_reservation');
Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
