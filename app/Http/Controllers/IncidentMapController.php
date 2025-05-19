<?php

// app/Http/Controllers/IncidentMapController.php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentMapController extends Controller
{
    public function index()
    {
        $incidents = Incident::with([
            'location.neighborhood.city',
            'incidentType',
            'incidentStatus',
        ])
            ->whereNotNull('location_id')
            ->get()
            ->map(function ($incident) {
                return [
                    'id' => $incident->id,
                    'description' => $incident->description,
                    'incident_type' => $incident->incidentType ? $incident->incidentType->name : null,
                    'incident_status' => $incident->incidentStatus ? $incident->incidentStatus->name : null,
                    'municipality' => $incident->location->neighborhood->city->name ?? null,
                    'lat' => $incident->location?->lat,
                    'lng' => $incident->location?->lng,
                    'created_at' => $incident->created_at ? $incident->created_at->format('Y-m-d') : null,
                ];
            });

        return response()->json($incidents);
    }
}

