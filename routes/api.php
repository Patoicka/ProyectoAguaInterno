<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\{
    FileController,
    UserController,
    LocationController,
    IncidentMapController,
    IncidentGraphController,
    DashboardController
};
use Src\Incident\Ports\Input\Controllers\IncidentController;

/*
|--------------------------------------------------------------------------
| API Routes – JSON & archivos
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', fn(Request $r) => $r->user());

/* ---------------- Utilidades & estáticos ---------------- */
Route::get('file/serve/{file}', [FileController::class, 'serveFile'])->middleware('signed')->name('file.serve');
Route::get('get-curp/{curp}',   [UserController::class, 'getCurp'])->name('user.getCurp');

Route::get('get-location-pc/{postalCode}',          [LocationController::class, 'getLocationByPostalCode']);
Route::get('get-location-state/{stateId}',          [LocationController::class, 'getLocationByState']);
Route::get('get-location-city/{cityId}',            [LocationController::class, 'getLocationByCity']);
Route::get('get-location-neighborhood/{neighborhoodId}', [LocationController::class, 'getLocationByNeighborhood']);

/* ---------------- Incidencias básicas ---------------- */
Route::get('incidents',      [IncidentController::class, 'index']);
Route::get('incidents/{id}', [IncidentController::class, 'show']);

/* ---------------- Visualizaciones ---------------- */
Route::get('map/incidents',         [IncidentMapController::class, 'index']);
Route::get('grafico/incidencias',   [IncidentGraphController::class, 'getIncidents']);

/* Alias compatibilidad Ziggy */
Route::get('/incidentMap',   [IncidentMapController::class, 'index'])->name('incident/Map');
Route::get('/incident-graph', [IncidentGraphController::class, 'getIncidents'])->name('incident/graph');

/* ---------------- End-points para el visualizador dinámico ---------------- */
Route::get('incident-chart',             [DashboardController::class, 'chartIncident'])->name('incident.chart');
Route::get('available-incident-filters', [DashboardController::class, 'getAvailableFilters'])->name('incident.available-filters');

Route::get('dashboard/incidencias/export-pdf', [DashboardController::class, 'exportPdf'])->name('incident.exportPdf');

/* --- consultas dinámicas (JSON · PDF · CSV) --- */
Route::get('dynamic-search',   [DashboardController::class, 'dynamicSearch'])->name('dynamic.search');
Route::get('dynamic-report',   [DashboardController::class, 'dynamicExportPdf'])->name('dynamic.report');
Route::get('dynamic-filters',  [DashboardController::class, 'dynamicAvailableFilters'])->name('dynamic.filters');
Route::get('table-columns',    [DashboardController::class, 'tableColumns'])->name('dynamic.table-columns');
Route::get('table-list',       [DashboardController::class, 'tableList'])->name('dynamic.table-list');
Route::get('/dynamic-search', fn() => Inertia::render('Visualizations/DynamicViewer'))->name('dynamic.search')->withoutMiddleware(['auth', 'api']);
