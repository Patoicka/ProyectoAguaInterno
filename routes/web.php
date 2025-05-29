<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\IncidentTypeController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');
Route::get('/welcome/incident/create', [WelcomeController::class, 'createIncidet'])->name('welcome.create.incidet');
Route::post('/welcome/incident/store', [WelcomeController::class, 'storeIncidet'])->name('welcome.store.incidet');
Route::get('/welcome/incident/search/{code?}', [WelcomeController::class, 'searchIncident'])->name('welcome.search.incident');
Route::get('/welcome/incident/show/{code?}', [WelcomeController::class, 'showIncident'])->name('welcome.show.incident');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile-photo', [ProfileController::class, 'destroyPhoto'])->name('profile.destroyPhoto');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Seguridad
    Route::resource('module', ModuleController::class)->parameters(['module' => 'module']);
    Route::resource('permission', PermissionController::class)->names('permission');
    Route::resource('role', RoleController::class)->parameters(['role' => 'role']);
    Route::resource('user', UserController::class)->parameters(['user' => 'user']);

    Route::resource('incidentType', IncidentTypeController::class)->parameters(['incidentType' => 'incidentType']);
    Route::resource('incident', IncidentController::class)->parameters(['incident' => 'incident']);
    Route::get('incident/assign/{incident}', [IncidentController::class, 'assign'])->name('incident.assign');
    Route::post('incident-assigned/{incident}', [IncidentController::class, 'assigned'])->name('incident.assigned');
    Route::post('incident-evidenced/{incident}', [IncidentController::class, 'evidenced'])->name('incident.evidenced');

    Route::get('/incidentMap', function () {
        return Inertia::render('Visualizations/IncidentMap');
    })->name('incident/Map');
    Route::get('/incident-graph', function () {
        return Inertia::render('Visualizations/IncidentGraph');
    })->name('incident.graph');
    Route::get(
        '/api/incident-chart',               // URI que ya existe en api.php
        [DashboardController::class, 'chartIncident']
    )->name('incident.chart');                      // ← NOMBRE que usa Vue

    Route::get(
        '/api/available-incident-filters',   // URI ya existente
        [DashboardController::class, 'getAvailableFilters']
    )->name('incident.available-filters');          // ← lo usa Vue

    Route::get(
        '/api/dashboard/incidencias/export-pdf', // URI ya existente
        [DashboardController::class, 'exportPdf']
    )->name('incident.exportPdf');
    Route::get('/dynamic-search', fn() => Inertia::render('Visualizations/DynamicSearch'));
    Route::get(
        '/api/dynamic-filters',
        [DashboardController::class, 'dynamicAvailableFilters']
    )->name('dynamic.filters')->middleware(['auth']);
});
require __DIR__ . '/auth.php';
