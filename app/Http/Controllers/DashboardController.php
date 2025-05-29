<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\IncidentStatus;
use App\Models\City;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Model;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* ============================================================
     *  DASHBOARD (Inertia)
     * ========================================================== */
    public function dashboard()
    {
        return Inertia::render('Dashboard', ['data' => null]);
    }

    /* ============================================================
     *  1.  GRÁFICA DE INCIDENCIAS
     * ========================================================== */
    public function chartIncident(Request $request)
    {
        $query = Incident::selectRaw("
                COUNT(*)                    AS total,
                incident_types.name         AS type,
                YEAR(incidents.created_at)  AS year,
                incident_statuses.name      AS status
            ")
            ->join('incident_types',    'incidents.incident_type_id',  '=', 'incident_types.id')
            ->join('incident_statuses', 'incidents.incident_status_id', '=', 'incident_statuses.id')
            ->join('locations',         'incidents.location_id',       '=', 'locations.id')
            ->join('neighborhoods',     'locations.neighborhood_id',   '=', 'neighborhoods.id')
            ->join('cities',            'neighborhoods.city_id',       '=', 'cities.id')
            ->groupBy('year', 'incident_types.name', 'incident_statuses.name')
            ->orderBy('year', 'asc');

        /* Filtros dinámicos */
        if ($request->filled('years'))    $query->whereIn(DB::raw('YEAR(incidents.created_at)'), (array) $request->years);
        if ($request->filled('types'))    $query->whereIn('incident_types.name',  (array) $request->types);
        if ($request->filled('statuses')) $query->whereIn('incident_statuses.name', (array) $request->statuses);
        if ($request->filled('cities'))   $query->whereIn('cities.name',          (array) $request->cities);

        $data   = $query->get();
        $years  = $data->pluck('year')->unique()->sort()->values()->toArray();
        $types  = $data->pluck('type')->unique()->sort()->values()->toArray();
        $datasets = [];

        if ($request->filled('types')) {
            /* Serie por ESTADO */
            $statuses = $data->pluck('status')->unique()->sort()->values()->toArray();
            foreach ($statuses as $status) {
                $statusData = $data->where('status', $status);
                $datasets[] = [
                    'label'           => $status,
                    'backgroundColor' => $this->generateRandomColor(),
                    'data'            => collect($years)->map(fn($y) => $statusData->where('year', $y)->sum('total'))
                ];
            }
        } else {
            /* Serie por TIPO */
            foreach ($types as $type) {
                $typeData = $data->where('type', $type);
                $datasets[] = [
                    'label'           => $type,
                    'backgroundColor' => $this->generateRandomColor(),
                    'data'            => collect($years)->map(fn($y) => $typeData->where('year', $y)->sum('total'))
                ];
            }
        }

        return response()->json(['labels' => $years, 'datasets' => $datasets]);
    }

    /* ============================================================
     *  2.  FILTROS DISPONIBLES
     * ========================================================== */
    public function getAvailableFilters()
    {
        $cities = Cache::remember(
            'available_cities',
            3600,
            fn() => City::pluck('name')->unique()->sort()->values()
        );

        return response()->json([
            'years'    => Incident::selectRaw('YEAR(created_at) AS year')->distinct()->orderBy('year')->pluck('year'),
            'types'    => IncidentType::pluck('name')->unique()->sort()->values(),
            'statuses' => IncidentStatus::pluck('name')->unique()->sort()->values(),
            'cities'   => $cities,
        ]);
    }

    /* ============================================================
     *  3.  PDF CLÁSICO DE INCIDENCIAS
     * ========================================================== */
    public function exportPdf(Request $request)
    {
        $anio     = (int) $request->input('anio');
        $tipos    = (array) $request->input('tipos',   []);
        $estados  = (array) $request->input('status',  []);
        $ciudades = (array) $request->input('city',    []);

        $incidencias = Incident::with([
            'report',
            'report.contact',
            'location.neighborhood.city',
            'incidentType',
            'incidentStatus',
        ])
            ->whereYear('created_at', $anio)
            ->when($tipos,    fn($q) => $q->whereHas('incidentType',   fn($qq) => $qq->whereIn('name', $tipos)))
            ->when($estados,  fn($q) => $q->whereHas('incidentStatus', fn($qq) => $qq->whereIn('name', $estados)))
            ->when($ciudades, fn($q) => $q->whereHas('location.neighborhood.city', fn($qq) => $qq->whereIn('name', $ciudades)))
            ->get();

        return Pdf::loadView('pdf.incidencias_por_anio', [
            'incidencias' => $incidencias,
            'anio'        => $anio,
            'tipos'       => $tipos,
        ])
            ->setPaper('a4', 'landscape')
            ->stream("incidencias_{$anio}.pdf");
    }

    /* ============================================================
     *  4.  EXPORTACIÓN DINÁMICA (PDF / CSV)
     * ========================================================== */
    public function dynamicExportPdf(Request $request)
    {
        /* ---------- parámetros ---------- */
        $table  = $request->string('table');
        $anio   = $request->integer('anio');
        $cols   = collect(explode(',', $request->string('cols')))->filter()->all();
        $limit  = $request->integer('limit', 500);
        $format = $request->string('format', 'pdf');
        $limitFromUser = $request->has('limit');

        /* ---------- resolver tabla/modelo ---------- */
        $tableName = class_exists($table) && is_subclass_of($table, Model::class)
            ? (new $table)->getTable()
            : $table;

        /* ---------- filtros extra ---------- */
        $extra = $request->except(['table', 'anio', 'cols', 'limit', 'format']);

        /* ---------- validar columnas ---------- */
        if ($cols) $cols = array_filter($cols, fn($c) => Schema::hasColumn($tableName, $c));

        /* ---------- builder base ---------- */
        $builder = $this->buildDynamicQuery($table, $extra, null, $anio, $cols ?: [], false);

        $total = (clone $builder)->count();

        /* ---------- CSV (si solicitado o si se excede límite sin override) ---------- */
        if ($format === 'csv' || (!$limitFromUser && $total > $limit)) {
            try {
                if (class_exists(SimpleExcelWriter::class)) {
                    return SimpleExcelWriter::streamDownload("{$tableName}.csv")
                        ->addRows($builder->cursor()->map(fn($r) => (array)$r));
                }
                return response()->streamDownload(function () use ($builder) {
                    $out = fopen('php://output', 'w');
                    $head = false;
                    foreach ($builder->cursor() as $r) {
                        if (!$head) {
                            fputcsv($out, array_keys((array)$r));
                            $head = true;
                        }
                        fputcsv($out, (array)$r);
                    }
                    fclose($out);
                }, "{$tableName}.csv", ['Content-Type' => 'text/csv']);
            } catch (\Throwable $e) {
                Log::error('CSV export failed', ['table' => $tableName, 'msg' => $e->getMessage()]);
                return response()->json(['error' => 'No se pudo generar el CSV'], 500);
            }
            if ($format === 'csv') return;
        }

        /* ---------- PDF ---------- */
        $rows = ($total > $limit) ? $builder->limit($limit)->get() : $builder->get();
        if (empty($cols)) $cols = $rows->first() ? array_keys((array)$rows->first()) : [];

        return Pdf::loadView('pdf.generic-report', [
            'rows' => $rows,
            'columns' => $cols,
            'table' => $tableName,
            'anio' => $anio,
            'total' => $total,
            'limit' => $limit,
        ])
            ->setPaper('a4', 'landscape')
            ->stream("{$tableName}_{$anio}.pdf");
    }

    /* ============================================================
     *  5.  NUEVA BÚSQUEDA DINÁMICA (JSON paginado)
     * ========================================================== */
    public function dynamicSearch(Request $request)
    {
        /* -- parámetros -- */
        $table    = $request->string('table');
        $perPage  = $request->integer('per_page', 25);
        $select   = collect(explode(',', $request->string('cols')))->filter()->all();
        $sort     = $request->string('sort');
        $dir      = strtolower($request->string('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $search   = $request->string('q');

        $extra = collect($request->query())
            ->except(['table', 'cols', 'per_page', 'page', 'sort', 'dir', 'q'])
            ->toArray();

        /* builder reutilizando helper */
        $builder  = $this->buildDynamicQuery($table, $extra, null, null, $select ?: [], false);
        $tableName = $builder->from;

        /* búsqueda libre */
        if ($search) {
            $textCols = collect(Schema::getColumnListing($tableName))->filter(function ($c) use ($tableName) {
                return in_array(Schema::getColumnType($tableName, $c), ['string', 'text', 'varchar', 'char']);
            });
            $builder->where(function ($q) use ($textCols, $search) {
                foreach ($textCols as $c) $q->orWhere($c, 'like', "%{$search}%");
            });
        }

        /* orden */
        if ($sort && Schema::hasColumn($tableName, $sort)) {
            $builder->orderBy($sort, $dir);
        }

        /* columnas específicas */
        if ($select) $builder->select($select);

        return response()->json($builder->paginate($perPage));
    }

    /* ============================================================
     *  6.  LISTA DE TABLAS
     * ========================================================== */
    public function tableList()
    {
        $db = DB::getDatabaseName();
        return response()->json(
            DB::table('information_schema.tables')
                ->where('table_schema', $db)
                ->pluck('table_name')
                ->values()
        );
    }

    /* ============================================================
     *  7.  HELPERS
     * ========================================================== */
    private function buildDynamicQuery(
        string $table,
        array $filters = [],
        ?string $yearColumn = null,
        ?int $year = null,
        array $select = [],
        bool $returnCollection = true
    ) {
        if (class_exists($table) && is_subclass_of($table, Model::class)) {
            $model  = new $table;
            $query  = $model->newQuery();
            $table  = $model->getTable();
        } else {
            abort_unless(Schema::hasTable($table), 404, 'Tabla desconocida');
            $query  = DB::table($table);
        }

        if ($select) $query->select($select);

        $yearColumn ??= Schema::hasColumn($table, 'created_at') ? 'created_at' : null;
        if ($yearColumn && $year) $query->whereYear($yearColumn, $year);

        foreach ($filters as $col => $val) {
            if (!Schema::hasColumn($table, $col)) continue;
            is_array($val) ? $query->whereIn($col, $val)
                : $query->where($col, $val);
        }

        return $returnCollection ? $query->get() : $query;
    }

    public function tableColumns(Request $request)
    {
        $table = $request->string('table');
        if (class_exists($table) && is_subclass_of($table, Model::class)) {
            $table = (new $table)->getTable();
        }
        abort_unless(Schema::hasTable($table), 404, 'Tabla desconocida');
        return response()->json(Schema::getColumnListing($table));
    }

    private function generateRandomColor(): string
    {
        return sprintf('rgba(%d,%d,%d,0.7)', mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
    }
    //  ─── en DashboardController ──────────────────────────────────────────────
    public function dynamicAvailableFilters(Request $request)
    {
        $table     = $request->string('table');
        $rawCols   = $request->input('cols');          // ej. "state_id,name"
        $columns   = $rawCols ? explode(',', $rawCols) : [];

        // resolver nombre real de tabla / modelo
        $tableName = class_exists($table) && is_subclass_of($table, Model::class)
            ? (new $table)->getTable()
            : $table;

        abort_unless(Schema::hasTable($tableName), 404, 'Tabla desconocida');

        // si no se pasan columnas → usa todas las columnas stringables (máx 6)
        if (!$columns) {
            $columns = collect(Schema::getColumnListing($tableName))
                ->take(6)                 // no inundar el select
                ->all();
        }

        $out = [];
        foreach ($columns as $col) {
            // solo columnas “pequeñas”: char, varchar, int, etc.
            if (!Schema::hasColumn($tableName, $col)) continue;

            $values = DB::table($tableName)
                ->select($col)
                ->distinct()
                ->limit(200)
                ->pluck($col)
                ->filter()
                ->values();

            // si la lista es muy grande no conviene ofrecerla
            if ($values->count() && $values->count() <= 200) {
                $out[$col] = $values;
            }
        }

        return response()->json($out);
    }
}
