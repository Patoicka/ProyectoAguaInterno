<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignRequest;
use App\Http\Requests\StoreEvidenceRequest;
use App\Models\Incident;
use App\Http\Requests\StoreIncidentRequest;
use App\Http\Requests\UpdateIncidentRequest;
use App\Mail\AssignReviewer;
use App\Models\Evidence;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\Report;
use App\Models\User;
use App\Services\IncidentService;
use App\Traits\Filterable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Response;
use Inertia\Inertia;

class IncidentController extends Controller
{
    use Filterable;

    private IncidentService $incidentService;
    private Incident $model;
    private string $source;
    private string $routeName;
    private string $module = 'incident';

    public function __construct(IncidentService $incidentService)
    {
        $this->middleware('auth');
        $this->incidentService = $incidentService;
        $this->source = 'Incident/';
        $this->model = new Incident();
        $this->routeName = 'incident.';

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        // $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['update', 'edit']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);

        $this->authorizeResource(Incident::class, $this->module);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $this->getFilters($request->query());
        $user = User::find(Auth::id());

        $query = Incident::query()->with('location', 'incidentType', 'incidentStatus', 'report');

        if (!$user->canPermission('incident.all')) {
            $query->where('reviewer_id', $user->id);
        }

        $query->when($request->search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', '%' . $search . '%')
                    ->orWhere('unique_code', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('report', function ($query) use ($search) {
                        $query->where('names', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('report', function ($query) use ($search) {
                        $query->where('first_surname', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('report', function ($query) use ($search) {
                        $query->where('second_surname', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('location.neighborhood', function ($query) use ($search) {
                        $query->where('postal_code', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('location.neighborhood.city', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('location.neighborhood.city.state', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('incidentType', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    })
                    ->orWhereHas('incidentStatus', function ($query) use ($search) {
                        $query->where('name', 'LIKE', '%' . $search . '%');
                    });
            });
        });

        $query->orderBy($filters['order'], $filters['direction']);
        $incidents = $query->paginate($filters['rows'])->withQueryString()->through(
            fn($incident) => [
                'id' => $incident->id,
                'description' => $incident->description,
                'code' => $incident->unique_code,
                'reporter' => $incident->report->full_name,
                'contact' => $incident->report->contact,
                'incidentType' => $incident->incidentType->name,
                'incidentStatus' => $incident->incidentStatus,
                'evidence' => $incident->evidence,
                'created_at' => $incident->created_at,
            ]
        );

        return Inertia::render("{$this->source}Index", [
            'title' => 'Control de incidencias',
            'routeName' => $this->routeName,
            'incidents' => $incidents,
            'filters' => $filters
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() // liston
    {
        return Inertia::render("{$this->source}Create", [
            'title' => 'Agregar incidencia',
            'incidentTypes' => IncidentType::where('status', true)->orderBy('name')->get(),
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentRequest $request) // liston
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

            return redirect()->route("{$this->routeName}index")->with('success', 'Incidencia registrada con éxito');
        } catch (Exception $e) {
            Log::error("Error al registrar la incidencia: " . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al registrar la incidencia. Intente nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident)
    {
        return Inertia::render("{$this->source}Show", [
            'title' => 'Revisar incidencia',
            'incident' => $incident->load('location.neighborhood.city', 'incidentType', 'incidentStatus', 'files', 'evidence.files'),
            'routeName' => $this->routeName,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident)
    {
        abort(404);
    }

    public function assign(Incident $incident) // liston
    {
        $this->authorize('assign', $incident);

        return Inertia::render("{$this->source}Assign", [
            'title' => 'Asignar revisor a incidencia',
            'users' => User::with('roles', 'file')->orderBy('name')->get(),
            'incident' => $incident->load('location.neighborhood.city', 'incidentType', 'incidentStatus', 'files'),
            'routeName' => $this->routeName,
        ]);
    }

    public function assigned(StoreAssignRequest $request, Incident $incident) // liston
    {
        $this->authorize('assign', $incident);

        $fields = $request->validated();
        $fields['incident_status_id'] = 2;
        $reviewer = User::findOrFail($fields['reviewer_id']);

        $incident->update($fields);
        Mail::to($reviewer->email)->queue(new AssignReviewer($reviewer->name, $incident->id));
        return redirect()->back()->with('success', 'Incidencia asignada con éxito');
    }

    public function evidenced(StoreEvidenceRequest $request, Incident $incident) // liston
    {
        $this->authorize('evidence', $incident);
        $evidence = Evidence::create($request->validated());
        $incident->update(['incident_status_id' => 3]);
        $this->incidentService->storeFileEvidence($request, $evidence);
        return redirect()->back()->with('success', 'Evidencia guardada con éxito');
    }

}
