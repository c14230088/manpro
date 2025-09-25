<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ItemsController;

Route::get('/', [UnitController::class, 'login'])->name('user.login');
Route::get('/processLogin/auth/google', [UnitController::class, 'processLogin'])->name('user.processLogin');
Route::get('/auth/google/callback', [UnitController::class, 'routeAdmin'])->name('user.processRouting');
Route::get('/logout', [UnitController::class, 'logout'])->name('user.logout');

Route::get('/booking', [UnitController::class, 'formBooking'])->name('user.booking.form');
Route::post('/booking', [UnitController::class, 'storeBooking'])->name('user.booking.request');

Route::get('/get/labs', [LabsController::class, 'getLabs'])->name('user.get.labs');
Route::get('/get/items', [ItemsController::class, 'getItems'])->name('user.get.items');

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/labs', [AdminController::class, 'labs'])->name('admin.labs');
    Route::get('/labs/{lab}/desks', [LabsController::class, 'getDesks'])->name('admin.labs.desks');
    // Route::get('/booking', [AdminController::class, 'listBooking'])->name('admin.booking.list');
});
