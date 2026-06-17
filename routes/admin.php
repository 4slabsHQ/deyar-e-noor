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
    Route::resource('countries',    CountryController::class);
    Route::resource('cities',       CityController::class);
    Route::resource('currencies',   CurrencyController::class);
    Route::resource('airlines',     AirlineController::class);
    Route::resource('hotels',       HotelController::class);
    Route::resource('transporters', TransporterController::class);
    Route::resource('guides',       GuideController::class);
    Route::resource('vendors',      VendorController::class);
    Route::resource('taxes',        TaxController::class);

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
    Route::resource('customers', CustomerController::class);
    Route::resource('suppliers', SupplierController::class);

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);

});