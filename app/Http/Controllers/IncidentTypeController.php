<?php

namespace App\Http\Controllers;

use App\Models\IncidentType;
use App\Http\Requests\StoreIncidentTypeRequest;
use App\Http\Requests\UpdateIncidentTypeRequest;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IncidentTypeController extends Controller
{
    use Filterable;

    private IncidentType $model;
    private string $source;
    private string $routeName;
    private string $module = 'incidentType';

    public function __construct()
    {
        $this->middleware('auth');
        $this->source = 'IncidentType/';
        $this->model = new IncidentType();
        $this->routeName = 'incidentType.';

        $this->middleware("permission:{$this->module}.index")->only(['index', 'show']);
        $this->middleware("permission:{$this->module}.store")->only(['store', 'create']);
        $this->middleware("permission:{$this->module}.update")->only(['update', 'edit']);
        $this->middleware("permission:{$this->module}.delete")->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $this->getFilters($request->query());
        $query = IncidentType::query();

        $query->when($request->search, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('description', 'LIKE', '%' . $search . '%');
            });
        });

        $query->orderBy($filters['order'], $filters['direction']);
        $incidentTypes = $query->paginate($filters['rows'])->withQueryString()->through(
            fn($incidentType) => [
                'id' => $incidentType->id,
                'name' => $incidentType->name,
                'description' => $incidentType->description,
                'status' => $incidentType->status,
                'created_at' => $incidentType->created_at,
            ]
        );

        return Inertia::render("{$this->source}Index", [
            'title'         => 'Gestión de tipos de incidencia',
            'routeName'     => $this->routeName,
            'incidentTypes' =>  $incidentTypes,
            'filters'       => $filters
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render("{$this->source}Create", [
            'title'          => 'Agregar tipo de incidencia',
            'routeName'      => $this->routeName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentTypeRequest $request)
    {
        IncidentType::create($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Tipo de incidencia creada con éxito!');
    }

    /**
     * Display the specified resource.
     */
    public function show(IncidentType $incidentType)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IncidentType $incidentType)
    {
        return Inertia::render("{$this->source}Edit", [
            'title'         => 'Editar tipo de incidencia',
            'routeName'     => $this->routeName,
            'incidentType'  => $incidentType,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentTypeRequest $request, IncidentType $incidentType)
    {
        $incidentType->update($request->validated());
        return redirect()->route("{$this->routeName}index")->with('success', 'Tipo de incidencia editada con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IncidentType $incidentType)
    {
        abort(404);
    }
}
