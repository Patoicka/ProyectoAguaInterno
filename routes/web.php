<?php

use App\Http\Controllers\{
    DashboardController,
    IncidentController,
    IncidentTypeController,
    ModuleController,
    RoleController,
    PermissionController,
    ProfileController,
    UserController,
    WelcomeController
};
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'welcome'])->name('welcome');

Route::get('/welcome/incident/create',  [WelcomeController::class, 'createIncidet'])->name('welcome.create.incidet');
Route::post('/welcome/incident/store',  [WelcomeController::class, 'storeIncidet'])->name('welcome.store.incidet');
Route::get('/welcome/incident/search/{code?}', [WelcomeController::class, 'searchIncident'])->name('welcome.search.incident');
Route::get('/welcome/incident/show/{code?}',  [WelcomeController::class, 'showIncident'])->name('welcome.show.incident');
Route::get('/api/incidentRegister', [WelcomeController::class, 'apiincidentRegister'])->name('welcome.incidentRegister');


/* =======================================================================
|  ZONA PRIVADA
| ===================================================================== */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    /* -------- Vistas Vue -------- */
    Route::get('/incidents/view', fn() => Inertia::render('SeeIncidents'))->name('incidents.view');

    /*  Mapa de incidencias – dos NAMES para compatibilidad  */
    Route::get('/incidentMap', fn() => Inertia::render('Visualizations/IncidentMap'))->name('incident.map');
    Route::get('/incidentMap', fn() => Inertia::render('Visualizations/IncidentMap'))->name('incident/Map');   // alias

    /*  Gráfica de barras – dos NAMES para compatibilidad  */
    Route::get('/incident-graph', fn() => Inertia::render('Visualizations/IncidentGraph'))->name('incident.graph');
    Route::get('/incident-graph', fn() => Inertia::render('Visualizations/IncidentGraph'))->name('incident/graph'); // alias

    Route::get('/graphic.index', fn() => Inertia::render('Visualizations/BarStacked'))->name('graphic.index');

    /* -------- Rutas para reportes “por ___” -------- */
    Route::get('/incidents/by-municipality', [IncidentController::class, 'byMunicipality'])
        ->name('incidents.byMunicipality');

    Route::get('/incidents/by-type',         [IncidentController::class, 'byType'])
        ->name('incidents.byType');

    Route::get('/incidents/by-status',       [IncidentController::class, 'byStatus'])
        ->name('incidents.byStatus');

    Route::get('/incidents/by-date',         [IncidentController::class, 'byDate'])
        ->name('incidents.byDate');

    /* -------- Perfil -------- */
    Route::get('/profile',          [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',        [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile-photo', [ProfileController::class, 'destroyPhoto'])->name('profile.destroyPhoto');
    Route::delete('/profile',       [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* -------- Seguridad / CRUD -------- */
    Route::resource('module',     ModuleController::class);
    Route::resource('permission', PermissionController::class)->names('permission');
    Route::resource('role',       RoleController::class);
    Route::resource('user',       UserController::class);

    /* -------- Incidencias CRUD -------- */
    Route::resource('incidentType', IncidentTypeController::class);
    Route::resource('incident',     IncidentController::class);
    Route::get('incident/assign/{incident}',   [IncidentController::class, 'assign'])->name('incident.assign');
    Route::post('incident-assigned/{incident}', [IncidentController::class, 'assigned'])->name('incident.assigned');
    Route::post('incident-evidenced/{incident}', [IncidentController::class, 'evidenced'])->name('incident.evidenced');

    /* -------- End-points que Vue llama con route() -------- */
    Route::get('/api/incident-chart',             [DashboardController::class, 'chartIncident'])->name('incident.chart');
    Route::get('/api/available-incident-filters', [DashboardController::class, 'getAvailableFilters'])->name('incident.available-filters');
    Route::get('/dashboard/incidencias/export-pdf', [DashboardController::class, 'exportPdf'])->name('incident.exportPdf');

    /* -------- Visualizador dinámico -------- */
    Route::get('/dynamic-search', fn() => Inertia::render('Visualizations/DynamicViewer'))->name('dynamic.search')->withoutMiddleware(['auth', 'api']);
    Route::get(
        '/dynamic-search',
        fn() => Inertia::render('Visualizations/DynamicViewer')
    )->name('dynamic-search');
    Route::prefix('/api')->group(function () {
        Route::get('/table-columns',   [DashboardController::class, 'tableColumns']);
        Route::get('/dynamic-filters', [DashboardController::class, 'dynamicAvailableFilters']);
        Route::get('/dynamic-search',  [DashboardController::class, 'dynamicSearch']);
        Route::get('/dynamic-report',  [DashboardController::class, 'dynamicExportPdf']);
    });
    Route::get(
        '/dynamic-search',
        fn() => Inertia::render('Visualizations/DynamicViewer')
    )->name('dynamic.search');
});

require __DIR__ . '/auth.php';
