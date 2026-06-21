<?php

use App\Http\Controllers\Admin\LeadActivityController;
use App\Http\Controllers\Admin\LeadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM Routes
|--------------------------------------------------------------------------
| This file is required from inside the existing "admin" route group in
| routes/web.php (the one wrapping countries/cities/currencies/etc.), so
| it already inherits the "admin" URL prefix, the "admin." name prefix,
| and your auth/admin middleware. No need to repeat them here.
*/

Route::prefix('leads')->name('leads.')->group(function () {

    Route::get('dashboard', [LeadController::class, 'dashboard'])
        ->name('dashboard')
        ->middleware('permission:leads.view');

    Route::post('{lead}/assign', [LeadController::class, 'assign'])
        ->name('assign')
        ->middleware('permission:leads.assign');

    Route::post('{lead}/change-status', [LeadController::class, 'changeStatus'])
        ->name('change-status')
        ->middleware('permission:leads.update');

    Route::post('{lead}/convert', [LeadController::class, 'convert'])
        ->name('convert')
        ->middleware('permission:leads.update');

    Route::post('{lead}/activities', [LeadActivityController::class, 'store'])
        ->name('activities.store')
        ->middleware('permission:leads.update');

    Route::delete('{lead}/activities/{activity}', [LeadActivityController::class, 'destroy'])
        ->name('activities.destroy')
        ->middleware('permission:leads.update');
});

Route::resource('leads', LeadController::class)
    ->except(['show'])
    ->middleware([
        'index'   => 'permission:leads.view',
        'create'  => 'permission:leads.create',
        'store'   => 'permission:leads.create',
        'edit'    => 'permission:leads.update',
        'update'  => 'permission:leads.update',
        'destroy' => 'permission:leads.delete',
    ]);

    Route::resource('channels', \App\Http\Controllers\Admin\ChannelController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:channels.view',
        'store'   => 'permission:channels.create',
        'update'  => 'permission:channels.update',
        'destroy' => 'permission:channels.delete',
    ]);

Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:campaigns.view',
        'store'   => 'permission:campaigns.create',
        'update'  => 'permission:campaigns.update',
        'destroy' => 'permission:campaigns.delete',
    ]);

Route::resource('lead-statuses', \App\Http\Controllers\Admin\LeadStatusController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:lead-statuses.view',
        'store'   => 'permission:lead-statuses.create',
        'update'  => 'permission:lead-statuses.update',
        'destroy' => 'permission:lead-statuses.delete',
    ]);

Route::resource('qualified-statuses', \App\Http\Controllers\Admin\QualifiedStatusController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:qualified-statuses.view',
        'store'   => 'permission:qualified-statuses.create',
        'update'  => 'permission:qualified-statuses.update',
        'destroy' => 'permission:qualified-statuses.delete',
    ]);

Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:services.view',
        'store'   => 'permission:services.create',
        'update'  => 'permission:services.update',
        'destroy' => 'permission:services.delete',
    ]);

Route::resource('sub-services', \App\Http\Controllers\Admin\SubServiceController::class)
    ->except(['show', 'create', 'edit'])
    ->middleware([
        'index'   => 'permission:sub-services.view',
        'store'   => 'permission:sub-services.create',
        'update'  => 'permission:sub-services.update',
        'destroy' => 'permission:sub-services.delete',
    ]);