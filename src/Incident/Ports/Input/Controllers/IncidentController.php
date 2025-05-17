<?php

namespace Src\Incident\Ports\Input\Controllers;

use Src\Incident\Application\Services\IncidentService;
use Illuminate\Http\Request;

class IncidentController
{
    public function __construct(private IncidentService $incidentService)
    {
    }

    public function index()
    {
        try {
            $incidents = $this->incidentService->all();
            $incidents = $incidents->map(function ($incident) {
                return $incident->toArray();
            });
            $results = count($incidents);
            return response()->json(['status' => 'success', 'results' => $results, 'data' => $incidents]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function show(int $id)
    {
        try {
            $incident = $this->incidentService->find($id);
            return response()->json(['status' => 'success', 'data' => $incident->toArray()]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}