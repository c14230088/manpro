<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LabsController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DesksController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SoftwareController;

Route::get('/', function () {
    return view('user.landing', ['title' => 'User | Landing Page']);
})->name('user.landing');

Route::get('/login', [UnitController::class, 'login'])->name('user.login');
Route::get('/processLogin/auth/google', [UnitController::class, 'processLogin'])->name('user.processLogin');
Route::get('/auth/google/callback', [UnitController::class, 'routeAdmin'])->name('user.processRouting');
Route::get('/logout', [UnitController::class, 'logout'])->name('user.logout');

Route::middleware(['auth.permission'])->group(function () {
    Route::get('/booking', [UnitController::class, 'formBooking'])->name('user.booking.form');
    Route::post('/booking', [UnitController::class, 'storeBooking'])->name('user.booking.request');

    Route::get('/get/labs', [LabsController::class, 'labsList'])->name('user.get.labs');
    Route::get('/get/items', [ItemsController::class, 'getItems'])->name('user.get.items');
    Route::get('/get/items/by-lab/{lab}', [ItemsController::class, 'getItemsByLab'])->name('user.get.items.by.lab');

    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/labs', [AdminController::class, 'labs'])->name('admin.labs');
        Route::get('/labs/list', [LabsController::class, 'labsList'])->name('admin.labs.list');
        Route::get('/labs/{lab}/desks', [LabsController::class, 'getDesks'])->name('admin.labs.desks');
        Route::post('/labs/{lab}/desks/update/location/{desk}', [DesksController::class, 'updateLocation'])->name('admin.labs.desks.update.location');
        Route::post('/labs/{lab}/desks/batch-create', [DesksController::class, 'batchCreate'])->name('admin.labs.desks.batch.create');
        Route::post('/labs/{lab}/desks/batch-delete', [DesksController::class, 'batchDelete'])->name('admin.labs.desks.batch.delete');

        // Route::get('/booking', [AdminController::class, 'listBooking'])->name('admin.booking.list');

        Route::get('/items', [AdminController::class, 'items'])->name('admin.items');
        Route::post('/items', [ItemsController::class, 'createItems'])->name('admin.items.create');
        Route::post('/items/set', [ItemsController::class, 'createItemSet'])->name('admin.items.set.create');

        Route::get('/items/filters', [ItemsController::class, 'getItemFilters'])->name('admin.items.filters'); // return all types dan all specsAttribute beserta valuesnya
        Route::get('/items/unaffiliated', [ItemsController::class, 'getUnaffiliatedItems'])->name('admin.items.unaffiliated');
        Route::post('/desks/{desk}/items', [DesksController::class, 'storeItems'])->name('admin.desks.store.items');

        Route::get('/items/{item}/details', [ItemsController::class, 'getItemDetails'])->name('admin.items.details');
        Route::get('/components/{component}/details', [ComponentsController::class, 'getComponentDetails'])->name('admin.components.details');

        Route::patch('/items/{item}/condition', [ItemsController::class, 'updateCondition'])->name('admin.items.updateCondition');
        Route::patch('/components/{component}/condition', [ComponentsController::class, 'updateComponentCondition'])->name('admin.items.updateComponentCondition');
        Route::post('/items/{item}/attach-desk/{desk}', [ItemsController::class, 'attachToDesk'])->name('admin.items.attachToDesk');

        // repair
        Route::get('/repairs', [RepairController::class, 'viewRepairs'])->name('admin.repairs');
        Route::patch('/repairs/{repair}/status', [RepairController::class, 'updateRepairStatus'])->name('admin.repairs.updateStatus');
        Route::post('/item/items/repair', [RepairController::class, 'applyRepair'])->name('admin.items.repair');
        
        // period
        Route::get('/periods', [PeriodController::class, 'periods'])->name('admin.periods');
        Route::post('/periods/create', [PeriodController::class, 'createPeriod'])->name('admin.periods.create');
        Route::patch('/periods/{id}/updateActive', [PeriodController::class, 'setActivePeriod'])->name('admin.periods.updateActive');

        // software
        Route::get('/softwares', [SoftwareController::class, 'softwares'])->name('admin.softwares');
        Route::post('/softwares/create', [SoftwareController::class, 'createSoftware'])->name('admin.softwares.create');
        Route::post('/softwares/{id}/update', [SoftwareController::class, 'editSoftware'])->name('admin.softwares.update');
        Route::post('/softwares/{id}/delete', [SoftwareController::class, 'deleteSoftware'])->name('admin.softwares.delete');

        // RBAC
        // Permissions
        Route::get('/permissions', [PermissionController::class, 'permissions'])->name('admin.permissions');
        Route::post('/permissions/update', [PermissionController::class, 'updateUserPermissions'])->name('admin.permissions.update');

        // Roles
        Route::get('/roles', [PermissionController::class, 'roles'])->name('admin.roles');
        Route::post('/roles/update', [PermissionController::class, 'updateUserRoles'])->name('admin.roles.update');
    });
});
