<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\City;
use App\Models\IncidentStatus;
use Illuminate\Support\Facades\Cache;
use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return Inertia::render("Dashboard", [
            'data' => null,
        ]);
    }

    public function chartIncident(Request $request)
    {
        $query = Incident::selectRaw('
                count(*) as total,
                incident_types.name as type,
                YEAR(incidents.created_at) as year,
                incident_statuses.name as status
            ')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->join('incident_statuses', 'incidents.incident_status_id', '=', 'incident_statuses.id')
            ->join('locations', 'incidents.location_id', '=', 'locations.id')
            ->join('neighborhoods', 'locations.neighborhood_id', '=', 'neighborhoods.id')
            ->join('cities', 'neighborhoods.city_id', '=', 'cities.id')
            ->groupBy('year', 'incident_types.name', 'incident_statuses.name')
            ->orderBy('year', 'asc');

        if ($request->filled('years')) {
            $query->whereIn(DB::raw('YEAR(incidents.created_at)'), (array) $request->years);
        }

        if ($request->filled('types')) {
            $query->whereIn('incident_types.name', (array) $request->types);
        }

        if ($request->filled('statuses')) {
            $query->whereIn('incident_statuses.name', (array) $request->statuses);
        }

        if ($request->filled('cities')) {
            $query->whereIn('cities.name', (array) $request->cities);
        }

        $data = $query->get();

        $years = $data->pluck('year')->unique()->sort()->values()->toArray();
        $types = $data->pluck('type')->unique()->sort()->values()->toArray();

        $datasets = [];

        if (request()->has('types')) {
            $statuses = $data->pluck('status')->unique()->sort()->values()->toArray();
            foreach ($statuses as $status) {
                $statusData = $data->where('status', $status);
                $backgroundColor = $this->generateRandomColor();
                $borderColor = 'rgba(75, 192, 192, 1)';
                $datasetData = [];

                foreach ($years as $year) {
                    $count = $statusData->where('year', $year)->sum('total');
                    $datasetData[] = $count;
                }

                $datasets[] = [
                    'label' => $status,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 1,
                    'data' => $datasetData,
                ];
            }

        } else {

            foreach ($types as $type) {
                $typeData = $data->where('type', $type);
                $backgroundColor = $this->generateRandomColor();
                $borderColor = 'rgba(75, 192, 192, 1)';
                $datasetData = [];

                foreach ($years as $year) {
                    $count = $typeData->where('year', $year)->sum('total');
                    $datasetData[] = $count;
                }

                $datasets[] = [
                    'label' => $type,
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'borderWidth' => 1,
                    'data' => $datasetData,
                ];
            }
        }

        return response()->json(['labels' => $years, 'datasets' => $datasets]);
    }

    public function getAvailableFilters()
    {
        $cities = Cache::remember('available_cities', 60 * 60, function () {
            return City::pluck('name')->unique()->sort()->values();
        });
        return response()->json([

            'years' => Incident::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year')->pluck('year'),
            'types' => IncidentType::pluck('name')->unique()->sort()->values(),
            'statuses' => IncidentStatus::pluck('name')->unique()->sort()->values(),
            'cities' => $cities,
        ]);
    }

    private function generateRandomColor()
    {
        $r = mt_rand(0, 255);
        $g = mt_rand(0, 255);
        $b = mt_rand(0, 255);
        return "rgba($r, $g, $b, 0.7)";
    }
}
