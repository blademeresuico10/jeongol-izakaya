<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReceptionistController;

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

Route::get('/menu_management', [AdminController::class, 'menu_management'])->name('admin.menu_management');
Route::post('/addmenu', [AdminController::class, 'addmenu'])->name('admin.addmenu');
Route::put('/menu_management/storeMenu', [AdminController::class, 'storeMenu'])->name('storeMenu');
Route::get('/editmenu/{id}', [AdminController::class, 'editMenu'])->name('admin.editmenu');
Route::put('/updatemenu/{id}', [AdminController::class, 'updateMenu'])->name('admin.updatemenu');

Route::get('/table_management', [AdminController::class, 'table_management'])->name('admin.table_management');
Route::post('/addtable', [AdminController::class, 'addtable'])->name('admin.addtable');
Route::put('/table_management/storeTable', [AdminController::class, 'storeTable'])->name('storeTable');
Route::get('/edittable/{id}', [AdminController::class, 'editTable'])->name('admin.edittable');
Route::put('/updatetable/{id}', [AdminController::class, 'updateTable'])->name('admin.updatetable');

Route::get('/stock_management', [AdminController::class, 'stock_management'])->name('admin.stock_management');

Route::get('/reports', function () {
    return view('admin.reports');
});


// Receptionist 
Route::middleware(['auth'])->group(function () {
    Route::get('/receptionist/home', [ReceptionistController::class, 'home'])->name('receptionist.home');
    Route::post('/receptionist/store-reservation', [ReceptionistController::class, 'storeReservation'])->name('receptionist.storeReservation');
    Route::get('/view_reservations', [ReceptionistController::class, 'reservations'])->name('receptionist.reservations');
   
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
