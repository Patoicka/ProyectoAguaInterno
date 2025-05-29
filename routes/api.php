<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Ejemplo
// Route::get('/students', function(){
//     return 'studenst list';
// });

Route::get('file/serve/{file}', [FileController::class, 'serveFile'])->name('file.serve')->middleware('signed');
Route::get('get-curp/{curp}', [UserController::class, 'getCurp'])->name('user.getCurp');
// Locacion
Route::get('get-location-pc/{postalCode}', [LocationController::class, 'getLocationByPostalCode'])->name('location.getLocationByPostalCode');
Route::get('get-location-state/{stateId}', [LocationController::class, 'getLocationByState'])->name('location.getLocationByState');
Route::get('get-location-city/{cityId}', [LocationController::class, 'getLocationByCity'])->name('location.getLocationByCity');
Route::get('get-location-neighborhood/{neighborhoodId}', [LocationController::class, 'getLocationByNeighborhood'])->name('location.getLocationByNeighborhood');
Route::get('/showIncident', [DashboardController::class, 'chartIncident'])->name('incident.showIncident')->withoutMiddleware(['auth', 'api']);
Route::get('/available-incident-filters', [DashboardController::class, 'getAvailableFilters'])->name('incident.available-filters')->withoutMiddleware(['auth', 'api']);
//Route::get('/dashboard/incidencias/export-pdf',[DashboardController::class, 'exportPorAnio'])->name('incident.exportPdf')->withoutMiddleware(['auth', 'api','can:incident.index']);
Route::get(
    '/dashboard/incidencias/export-pdf',
    [DashboardController::class, 'exportPdf']
)->name('incident.exportPdf')
    ->withoutMiddleware(['auth', 'api', 'can:incident.index']);
Route::get(
    'dynamic-report',                               // ← sin slash inicial
    [App\Http\Controllers\DashboardController::class, 'dynamicExportPdf']
)->name('dynamic.report')                                  // nombre interno opcional
    ->withoutMiddleware(['auth', 'api']);
Route::get(
    'table-columns',
    [App\Http\Controllers\DashboardController::class, 'tableColumns']
)->name('dynamic.table-columns')
    ->withoutMiddleware(['auth', 'api']);
Route::get(
    'table-list',
    [App\Http\Controllers\DashboardController::class, 'tableList']
)->name('dynamic.table-list')
    ->withoutMiddleware(['auth', 'api']);
Route::get('dynamic-search',  [DashboardController::class, 'dynamicSearch'])->name('dynamic.search')->withoutMiddleware(['auth', 'api']);
