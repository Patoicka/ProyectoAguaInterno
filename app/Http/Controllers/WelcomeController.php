<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidentRequest;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\Report;
use App\Services\IncidentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    protected string $routeName;
    protected string $source;
    private IncidentService $incidentService;

    public function __construct(IncidentService $incidentService)
    {
        $this->incidentService = $incidentService;
        $this->routeName = "welcome.";
        $this->source    = "Welcome/";
    }

    public function welcome(Request $request): Response
    {
        return Inertia::render("{$this->source}Home/Index", [
            'title'   => 'Bienvenido',
            'routeName' => $this->routeName,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    }

    public function createIncidet()
    {
        return Inertia::render("{$this->source}Incident/Create", [
            'title'   => 'Generar nueva incidencia',
            'incidentTypes'  => IncidentType::where('status', true)->orderBy('name')->get(),
            'routeName' => $this->routeName,
        ]);
    }

    public function storeIncidet(StoreIncidentRequest $request)
    {
        try {
            $fields = $request->validated();
            $report = Report::create($fields);
            $report->contact()->create($fields);

            $fields['report_id'] = $report->id;
            $fields['location_id'] = Location::create($fields)->id;
            $fields['incident_status_id'] = 1;
            $fields['unique_code'] = $this->incidentService->getUniqueCode($request);

            $incident = Incident::create($fields);
            $this->incidentService->storeFile($request, $incident);
            $this->incidentService->sendEmail($incident);

            return redirect()->route("{$this->routeName}show.incident", ['code' => $incident->unique_code])
                ->with('success', 'Incidencia registrada con éxito');
        } catch (Exception $e) {
            Log::error("Error al registrar la incidencia: " . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al registrar la incidencia. Intente nuevamente.');
        }
    }

    public function searchIncident(?string $code = null)

    {
        return Inertia::render("{$this->source}Incident/Search", [
            'title'     => 'Buscador de incidencias',
            'code'      => $code,
            'routeName' => $this->routeName,
        ]);
    }

    public function showIncident(string $code)
    {
        $incident = Incident::with([
            'location.neighborhood.city',
            'incidentType',
            'incidentStatus',
            'evidence.files',
            'files'
        ])
            ->where('unique_code', $code)
            ->first();

        return Inertia::render("{$this->source}Incident/Show", [
            'title' => 'Ver documento',
            'incident' => $incident
        ]);
    }
}
