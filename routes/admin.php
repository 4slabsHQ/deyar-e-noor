<?php

use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
// Admin Controllers
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CareOffController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\FormOwnerController;
use App\Http\Controllers\Admin\GuideController;
use App\Http\Controllers\Admin\HotelController;
use App\Http\Controllers\Admin\MaktabCategoryController;
use App\Http\Controllers\Admin\MehramRelationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\TransporterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\WarisRelationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
    Route::resource('countries', CountryController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:countries.view',
            'create' => 'permission:countries.create',
            'store' => 'permission:countries.create',
            'edit' => 'permission:countries.update',
            'update' => 'permission:countries.update',
            'destroy' => 'permission:countries.delete',
        ]);
    Route::resource('cities', CityController::class);
    Route::resource('currencies', CurrencyController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:currencies.view',
            'create' => 'permission:currencies.create',
            'store' => 'permission:currencies.create',
            'edit' => 'permission:currencies.update',
            'update' => 'permission:currencies.update',
            'destroy' => 'permission:currencies.delete',
        ]);
    Route::resource('airlines', AirlineController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:airlines.view',
            'create' => 'permission:airlines.create',
            'store' => 'permission:airlines.create',
            'edit' => 'permission:airlines.update',
            'update' => 'permission:airlines.update',
            'destroy' => 'permission:airlines.delete',
        ]);
    Route::resource('airports', AirportController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:airports.view',
            'create' => 'permission:airports.create',
            'store' => 'permission:airports.create',
            'edit' => 'permission:airports.update',
            'update' => 'permission:airports.update',
            'destroy' => 'permission:airports.delete',
        ]);
    Route::resource('hotels', HotelController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:hotels.view',
            'create' => 'permission:hotels.create',
            'store' => 'permission:hotels.create',
            'edit' => 'permission:hotels.update',
            'update' => 'permission:hotels.update',
            'destroy' => 'permission:hotels.delete',
        ]);
    Route::resource('transporters', TransporterController::class);
    Route::resource('guides', GuideController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('taxes', TaxController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:taxes.view',
            'create' => 'permission:taxes.create',
            'store' => 'permission:taxes.create',
            'edit' => 'permission:taxes.update',
            'update' => 'permission:taxes.update',
            'destroy' => 'permission:taxes.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Hajj Masters
    |--------------------------------------------------------------------------
    */
    Route::resource('form-owners', FormOwnerController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:form-owners.view',
            'create' => 'permission:form-owners.create',
            'store' => 'permission:form-owners.create',
            'edit' => 'permission:form-owners.update',
            'update' => 'permission:form-owners.update',
            'destroy' => 'permission:form-owners.delete',
        ]);
    Route::resource('maktab-categories', MaktabCategoryController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:maktab-categories.view',
            'create' => 'permission:maktab-categories.create',
            'store' => 'permission:maktab-categories.create',
            'edit' => 'permission:maktab-categories.update',
            'update' => 'permission:maktab-categories.update',
            'destroy' => 'permission:maktab-categories.delete',
        ]);
    Route::resource('care-offs', CareOffController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:care-offs.view',
            'create' => 'permission:care-offs.create',
            'store' => 'permission:care-offs.create',
            'edit' => 'permission:care-offs.update',
            'update' => 'permission:care-offs.update',
            'destroy' => 'permission:care-offs.delete',
        ]);
    Route::resource('packages', PackageController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:packages.view',
            'create' => 'permission:packages.create',
            'store' => 'permission:packages.create',
            'edit' => 'permission:packages.update',
            'update' => 'permission:packages.update',
            'destroy' => 'permission:packages.delete',
        ]);
    Route::resource('room-types', RoomTypeController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:room-types.view',
            'create' => 'permission:room-types.create',
            'store' => 'permission:room-types.create',
            'edit' => 'permission:room-types.update',
            'update' => 'permission:room-types.update',
            'destroy' => 'permission:room-types.delete',
        ]);
    Route::resource('mehram-relations', MehramRelationController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:mehram-relations.view',
            'create' => 'permission:mehram-relations.create',
            'store' => 'permission:mehram-relations.create',
            'edit' => 'permission:mehram-relations.update',
            'update' => 'permission:mehram-relations.update',
            'destroy' => 'permission:mehram-relations.delete',
        ]);
    Route::resource('waris-relations', WarisRelationController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:waris-relations.view',
            'create' => 'permission:waris-relations.create',
            'store' => 'permission:waris-relations.create',
            'edit' => 'permission:waris-relations.update',
            'update' => 'permission:waris-relations.update',
            'destroy' => 'permission:waris-relations.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    */
    Route::resource('companies', CompanyController::class)->except('show');
    Route::resource('branches', BranchController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('employees', EmployeeController::class);

    /*
    |--------------------------------------------------------------------------
    | Parties
    |--------------------------------------------------------------------------
    */
    Route::resource('customers', CustomerController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:customers.view',
            'create' => 'permission:customers.create',
            'store' => 'permission:customers.create',
            'edit' => 'permission:customers.update',
            'update' => 'permission:customers.update',
            'destroy' => 'permission:customers.delete',
        ]);
    Route::resource('suppliers', SupplierController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:suppliers.view',
            'create' => 'permission:suppliers.create',
            'store' => 'permission:suppliers.create',
            'edit' => 'permission:suppliers.update',
            'update' => 'permission:suppliers.update',
            'destroy' => 'permission:suppliers.delete',
        ]);

    /*
    |--------------------------------------------------------------------------
    | Access Control
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class)
        ->middleware([
            'index' => 'permission:users.view',
            'create' => 'permission:users.create',
            'store' => 'permission:users.create',
            'edit' => 'permission:users.update',
            'update' => 'permission:users.update',
        ]);

    // Roles
    Route::resource('roles', RoleController::class)
        ->middleware([
            'index' => 'permission:roles.view',
            'show' => 'permission:roles.view',
            'create' => 'permission:roles.create',
            'store' => 'permission:roles.create',
            'edit' => 'permission:roles.update',
            'update' => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);

    // Permissions
    Route::resource('permissions', PermissionController::class)
        ->middleware([
            'index' => 'permission:roles.view',
            'create' => 'permission:roles.create',
            'store' => 'permission:roles.create',
            'edit' => 'permission:roles.update',
            'update' => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);

    require __DIR__.'/crm.php';
});
