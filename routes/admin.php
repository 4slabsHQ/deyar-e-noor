<?php

use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\CareOffController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\FlightController;
use App\Http\Controllers\Admin\FormOwnerController;
use App\Http\Controllers\Admin\MaktabCategoryController;
use App\Http\Controllers\Admin\MehramRelationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PilgrimController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarisRelationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
    Route::resource('cities', CityController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:cities.view',
            'create' => 'permission:cities.create',
            'store' => 'permission:cities.create',
            'edit' => 'permission:cities.update',
            'update' => 'permission:cities.update',
            'destroy' => 'permission:cities.delete',
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

    Route::resource('companies', CompanyController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:companies.view',
            'create' => 'permission:companies.create',
            'store' => 'permission:companies.create',
            'edit' => 'permission:companies.edit',
            'update' => 'permission:companies.edit',
            'destroy' => 'permission:companies.destroy',
        ]);
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

    Route::get('pilgrims/preview-family-code', [PilgrimController::class, 'previewFamilyCode'])
        ->name('pilgrims.preview-family-code')
        ->middleware('permission:pilgrims.create|pilgrims.update');

    Route::get('pilgrims/families', [PilgrimController::class, 'families'])
        ->name('pilgrims.families')
        ->middleware('permission:pilgrims.create|pilgrims.update');

    Route::resource('pilgrims', PilgrimController::class)
        ->middleware([
            'index' => 'permission:pilgrims.view',
            'show' => 'permission:pilgrims.view',
            'create' => 'permission:pilgrims.create',
            'store' => 'permission:pilgrims.create',
            'edit' => 'permission:pilgrims.update',
            'update' => 'permission:pilgrims.update',
            'destroy' => 'permission:pilgrims.delete',
        ]);

    Route::resource('flights', FlightController::class)
        ->except(['show'])
        ->middleware([
            'index' => 'permission:flights.view',
            'create' => 'permission:flights.create',
            'store' => 'permission:flights.create',
            'edit' => 'permission:flights.update',
            'update' => 'permission:flights.update',
            'destroy' => 'permission:flights.delete',
        ]);

    Route::resource('users', UserController::class)
        ->middleware([
            'index' => 'permission:users.view',
            'create' => 'permission:users.create',
            'store' => 'permission:users.create',
            'edit' => 'permission:users.update',
            'update' => 'permission:users.update',
        ]);

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

    Route::resource('permissions', PermissionController::class)
        ->middleware([
            'index' => 'permission:roles.view',
            'create' => 'permission:roles.create',
            'store' => 'permission:roles.create',
            'edit' => 'permission:roles.update',
            'update' => 'permission:roles.update',
            'destroy' => 'permission:roles.delete',
        ]);
});
