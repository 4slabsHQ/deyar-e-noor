<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Admin Controllers
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\TransporterController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Master Data
    |--------------------------------------------------------------------------
    */
    Route::resource('countries', \App\Http\Controllers\Admin\CountryController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:countries.view',
            'create'  => 'permission:countries.create',
            'store'   => 'permission:countries.create',
            'edit'    => 'permission:countries.update',
            'update'  => 'permission:countries.update',
            'destroy' => 'permission:countries.delete',
        ]);
    Route::resource('cities',       CityController::class);
    Route::resource('currencies',   CurrencyController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:currencies.view',
            'create'  => 'permission:currencies.create',
            'store'   => 'permission:currencies.create',
            'edit'    => 'permission:currencies.update',
            'update'  => 'permission:currencies.update',
            'destroy' => 'permission:currencies.delete',
        ]);
    Route::resource('airlines', \App\Http\Controllers\Admin\AirlineController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:airlines.view',
            'create'  => 'permission:airlines.create',
            'store'   => 'permission:airlines.create',
            'edit'    => 'permission:airlines.update',
            'update'  => 'permission:airlines.update',
            'destroy' => 'permission:airlines.delete',
        ]);
    Route::resource('hotels', \App\Http\Controllers\Admin\HotelController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:hotels.view',
            'create'  => 'permission:hotels.create',
            'store'   => 'permission:hotels.create',
            'edit'    => 'permission:hotels.update',
            'update'  => 'permission:hotels.update',
            'destroy' => 'permission:hotels.delete',
        ]);
    Route::resource('transporters', TransporterController::class);
    Route::resource('guides',       GuideController::class);
    Route::resource('vendors',      VendorController::class);
    Route::resource('taxes', \App\Http\Controllers\Admin\TaxController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:taxes.view',
            'create'  => 'permission:taxes.create',
            'store'   => 'permission:taxes.create',
            'edit'    => 'permission:taxes.update',
            'update'  => 'permission:taxes.update',
            'destroy' => 'permission:taxes.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */
    Route::resource('companies', CompanyController::class)->except('show');
    Route::resource('branches',    BranchController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees',   EmployeeController::class);

    /*
    |--------------------------------------------------------------------------
    | Parties
    |--------------------------------------------------------------------------
    */
    Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:customers.view',
            'create'  => 'permission:customers.create',
            'store'   => 'permission:customers.create',
            'edit'    => 'permission:customers.update',
            'update'  => 'permission:customers.update',
            'destroy' => 'permission:customers.delete',
        ]);
    Route::resource('suppliers', \App\Http\Controllers\Admin\SupplierController::class)
        ->except(['show'])
        ->middleware([
            'index'   => 'permission:suppliers.view',
            'create'  => 'permission:suppliers.create',
            'store'   => 'permission:suppliers.create',
            'edit'    => 'permission:suppliers.update',
            'update'  => 'permission:suppliers.update',
            'destroy' => 'permission:suppliers.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class)
        ->middleware([
            'index'  => 'permission:users.view',
            'create' => 'permission:users.create',
            'store'  => 'permission:users.create',
            'edit'   => 'permission:users.update',
            'update' => 'permission:users.update',
        ]);

    // Roles
    Route::resource('roles', RoleController::class)
        ->middleware([
            'index'   => 'permission:roles.view',
            'show'    => 'permission:roles.view',
            'create'  => 'permission:roles.create',
            'store'   => 'permission:roles.create',
            'edit'    => 'permission:roles.update',
            'update'  => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);

    // Permissions
    Route::resource('permissions', PermissionController::class)
        ->middleware([
            'index'   => 'permission:roles.view',
            'create'  => 'permission:roles.create',
            'store'   => 'permission:roles.create',
            'edit'    => 'permission:roles.update',
            'update'  => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);

        require __DIR__ . '/crm.php';
});