<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IncidentGraphController extends Controller
{
    public function getIncidents(Request $request)
    {
        $limite = $request->get('limit', null);

        $query = DB::table('incidents')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->join('incident_statuses', 'incidents.incident_status_id', '=', 'incident_statuses.id')
            ->select(
                'incident_types.name as tipo',
                'incident_statuses.name as estatus',
                DB::raw('count(*) as total')
            )
            ->groupBy('tipo', 'estatus')
            ->orderBy('tipo');

        if ($limite) {
            $query->limit($limite);
        }

        $datos = $query->get();

        return response()->json($datos);
    }
}