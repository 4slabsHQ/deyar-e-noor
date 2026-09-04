<?php

use App\Http\Controllers\Admin\AccommodationPlanController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AirportController;
use App\Http\Controllers\Admin\CareOffController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\FlightAssignmentController;
use App\Http\Controllers\Admin\FlightController;
use App\Http\Controllers\Admin\FormOwnerController;
use App\Http\Controllers\Admin\HajjSeasonController;
use App\Http\Controllers\Admin\MaktabCategoryController;
use App\Http\Controllers\Admin\MehramRelationController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PilgrimController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomTypeController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WarisRelationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'active'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('countries', CountryController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:countries.view')
        ->middlewareFor(['create', 'store'], 'permission:countries.create')
        ->middlewareFor(['edit', 'update'], 'permission:countries.update')
        ->middlewareFor('destroy', 'permission:countries.delete');
    Route::resource('cities', CityController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:cities.view')
        ->middlewareFor(['create', 'store'], 'permission:cities.create')
        ->middlewareFor(['edit', 'update'], 'permission:cities.update')
        ->middlewareFor('destroy', 'permission:cities.delete');
    Route::resource('airlines', AirlineController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:airlines.view')
        ->middlewareFor(['create', 'store'], 'permission:airlines.create')
        ->middlewareFor(['edit', 'update'], 'permission:airlines.update')
        ->middlewareFor('destroy', 'permission:airlines.delete');
    Route::resource('airports', AirportController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:airports.view')
        ->middlewareFor(['create', 'store'], 'permission:airports.create')
        ->middlewareFor(['edit', 'update'], 'permission:airports.update')
        ->middlewareFor('destroy', 'permission:airports.delete');

    Route::resource('companies', CompanyController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:companies.view')
        ->middlewareFor(['create', 'store'], 'permission:companies.create')
        ->middlewareFor(['edit', 'update'], 'permission:companies.edit')
        ->middlewareFor('destroy', 'permission:companies.destroy');
    Route::resource('form-owners', FormOwnerController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:form-owners.view')
        ->middlewareFor(['create', 'store'], 'permission:form-owners.create')
        ->middlewareFor(['edit', 'update'], 'permission:form-owners.update')
        ->middlewareFor('destroy', 'permission:form-owners.delete');
    Route::resource('maktab-categories', MaktabCategoryController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:maktab-categories.view')
        ->middlewareFor(['create', 'store'], 'permission:maktab-categories.create')
        ->middlewareFor(['edit', 'update'], 'permission:maktab-categories.update')
        ->middlewareFor('destroy', 'permission:maktab-categories.delete');
    Route::resource('care-offs', CareOffController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:care-offs.view')
        ->middlewareFor(['create', 'store'], 'permission:care-offs.create')
        ->middlewareFor(['edit', 'update'], 'permission:care-offs.update')
        ->middlewareFor('destroy', 'permission:care-offs.delete');
    Route::resource('packages', PackageController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:packages.view')
        ->middlewareFor(['create', 'store'], 'permission:packages.create')
        ->middlewareFor(['edit', 'update'], 'permission:packages.update')
        ->middlewareFor('destroy', 'permission:packages.delete');
    Route::resource('properties', PropertyController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:properties.view')
        ->middlewareFor(['create', 'store'], 'permission:properties.create')
        ->middlewareFor(['edit', 'update'], 'permission:properties.update')
        ->middlewareFor('destroy', 'permission:properties.delete');
    Route::resource('routes', RouteController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:routes.view')
        ->middlewareFor(['create', 'store'], 'permission:routes.create')
        ->middlewareFor(['edit', 'update'], 'permission:routes.update')
        ->middlewareFor('destroy', 'permission:routes.delete');
    Route::resource('accommodation-plans', AccommodationPlanController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:accommodation-plans.view')
        ->middlewareFor(['create', 'store'], 'permission:accommodation-plans.create')
        ->middlewareFor(['edit', 'update'], 'permission:accommodation-plans.update')
        ->middlewareFor('destroy', 'permission:accommodation-plans.delete');
    Route::resource('room-types', RoomTypeController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:room-types.view')
        ->middlewareFor(['create', 'store'], 'permission:room-types.create')
        ->middlewareFor(['edit', 'update'], 'permission:room-types.update')
        ->middlewareFor('destroy', 'permission:room-types.delete');
    Route::resource('mehram-relations', MehramRelationController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:mehram-relations.view')
        ->middlewareFor(['create', 'store'], 'permission:mehram-relations.create')
        ->middlewareFor(['edit', 'update'], 'permission:mehram-relations.update')
        ->middlewareFor('destroy', 'permission:mehram-relations.delete');
    Route::resource('waris-relations', WarisRelationController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:waris-relations.view')
        ->middlewareFor(['create', 'store'], 'permission:waris-relations.create')
        ->middlewareFor(['edit', 'update'], 'permission:waris-relations.update')
        ->middlewareFor('destroy', 'permission:waris-relations.delete');

    Route::get('pilgrims/preview-family-code', [PilgrimController::class, 'previewFamilyCode'])
        ->name('pilgrims.preview-family-code')
        ->middleware('permission:pilgrims.create|pilgrims.update');

    Route::get('pilgrims/families', [PilgrimController::class, 'families'])
        ->name('pilgrims.families')
        ->middleware('permission:pilgrims.create|pilgrims.update');

    Route::get('pilgrims/{pilgrim}/deletion-preview', [PilgrimController::class, 'deletionPreview'])
        ->name('pilgrims.deletion-preview')
        ->middleware('permission:pilgrims.delete');

    Route::resource('pilgrims', PilgrimController::class)
        ->middlewareFor(['index', 'show'], 'permission:pilgrims.view')
        ->middlewareFor(['create', 'store'], 'permission:pilgrims.create')
        ->middlewareFor(['edit', 'update'], 'permission:pilgrims.update')
        ->middlewareFor('destroy', 'permission:pilgrims.delete');

    Route::resource('flights', FlightController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:flights.view')
        ->middlewareFor(['create', 'store'], 'permission:flights.create')
        ->middlewareFor(['edit', 'update'], 'permission:flights.update')
        ->middlewareFor('destroy', 'permission:flights.delete');

    Route::get('flight-assignments', [FlightAssignmentController::class, 'index'])
        ->name('flight-assignments.index')
        ->middleware('permission:flights.assign');

    Route::get('flight-assignments/{flight}/workspace', [FlightAssignmentController::class, 'workspace'])
        ->name('flight-assignments.workspace')
        ->middleware('permission:flights.assign');

    Route::get('flight-assignments/{flight}/results', [FlightAssignmentController::class, 'results'])
        ->name('flight-assignments.results')
        ->middleware('permission:flights.assign');

    Route::get('flight-assignments/{flight}', [FlightAssignmentController::class, 'show'])
        ->name('flight-assignments.show')
        ->middleware('permission:flights.assign');

    Route::post('flight-assignments/{flight}', [FlightAssignmentController::class, 'store'])
        ->name('flight-assignments.store')
        ->middleware('permission:flights.assign');

    Route::get('reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('permission:reports.view');

    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])
        ->name('reports.export.excel')
        ->middleware('permission:reports.export');

    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf')
        ->middleware('permission:reports.export');

    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])
        ->name('reports.export.csv')
        ->middleware('permission:reports.export');

    Route::get('reports/{report}/results', [ReportController::class, 'results'])
        ->name('reports.results')
        ->where('report', '[a-z_]+')
        ->middleware('permission:reports.view');

    Route::get('reports/{report}', [ReportController::class, 'show'])
        ->name('reports.show')
        ->where('report', '[a-z_]+')
        ->middleware('permission:reports.view');

    Route::post('reports/{report}/columns', [ReportController::class, 'saveColumns'])
        ->name('reports.columns.save')
        ->where('report', '[a-z_]+')
        ->middleware('permission:reports.view');

    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middlewareFor('index', 'permission:users.view')
        ->middlewareFor(['create', 'store'], 'permission:users.create')
        ->middlewareFor(['edit', 'update'], 'permission:users.update')
        ->middlewareFor('destroy', 'permission:users.delete');

    Route::put('users/{user}/password', [UserController::class, 'updatePassword'])
        ->name('users.password.update')
        ->middleware('permission:users.update');

    Route::resource('roles', RoleController::class)
        ->middlewareFor(['index', 'show'], 'permission:roles.view')
        ->middlewareFor(['create', 'store'], 'permission:roles.create')
        ->middlewareFor(['edit', 'update'], 'permission:roles.update')
        ->middlewareFor('destroy', 'permission:roles.delete');

    Route::resource('permissions', PermissionController::class)
        ->middlewareFor('index', 'permission:roles.view')
        ->middlewareFor(['create', 'store'], 'permission:roles.create')
        ->middlewareFor(['edit', 'update'], 'permission:roles.update')
        ->middlewareFor('destroy', 'permission:roles.delete');

    Route::get('hajj-seasons', [HajjSeasonController::class, 'index'])
        ->name('hajj-seasons.index')
        ->middleware('permission:hajj-seasons.view');

    Route::post('hajj-seasons', [HajjSeasonController::class, 'store'])
        ->name('hajj-seasons.store')
        ->middleware('permission:hajj-seasons.manage');

    Route::post('hajj-seasons/{hajjSeason}/activate', [HajjSeasonController::class, 'activate'])
        ->name('hajj-seasons.activate')
        ->middleware('permission:hajj-seasons.manage');

    Route::delete('hajj-seasons/{hajjSeason}', [HajjSeasonController::class, 'destroy'])
        ->name('hajj-seasons.destroy')
        ->middleware('permission:hajj-seasons.manage');
});
