<?php

namespace App\Services;

use App\Mail\NewIncident;
use App\Mail\SendIncident;
use App\Models\Evidence;
use App\Models\File;
use App\Models\Incident;
use App\Models\Neighborhood;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class IncidentService
{
    protected $incident;

    public function __construct(Incident $incident)
    {
        $this->incident = $incident;
    }

    public function storeFile(Request $request, Incident $incident)
    {
        $this->storeFiles($request, $incident, 'incidents');
    }

    public function storeFileEvidence(Request $request, Evidence $evidence)
    {
        $this->storeFiles($request, $evidence, 'evidence');
    }

    public function getUniqueCode(Request $request)
    {
        /* $neighborhood = Neighborhood::find($request->neighborhood_id);
        $year = substr(now()->year, -2);
        $stateCode = $neighborhood->city->state->code;
        $cityCode = $neighborhood->city->code;
        $nextIncident = (Incident::max('id') ?? 0) + 1;
        return "{$stateCode}{$cityCode}{$neighborhood->code}{$year}-{$nextIncident}"; */
        $neighborhood = Neighborhood::find($request->neighborhood_id);

        if (!$neighborhood) {
            return response()->json(['error' => 'Neighborhood not found'], 404);
        }

        $stateCode = $neighborhood->city->state->code;
        $cityCode = $neighborhood->city->code;
        $neighborhoodCode = $neighborhood->code;

        // Obtener la fecha actual en formato día-mes-año
        $currentDate = now()->format('d-m-Y');

        // Obtener el siguiente ID de incidente
        $nextIncident = (Incident::max('id') ?? 0) + 1;

        return "{$stateCode}{$cityCode}{$neighborhood->code}-{$currentDate}-{$nextIncident}"; 
    }

    public function sendEmail(Incident $incident)
    {
        $allAdmins = User::role(1)->get();

        foreach ($allAdmins as $admin) {
            Mail::to($admin->email)->queue(new NewIncident($admin->name, $incident->incidentType->name, $incident->id));
        }

        Mail::to($incident->report->contact->contact_email)
            ->queue(new SendIncident($incident->report->names, $incident->incidentType->name, $incident->unique_code));
    }

    private function storeFiles(Request $request, $model, string $directory)
    {
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = ((File::max('id') ?? 0) + 1) . '-' . $file->getClientOriginalName();
                $filePath = $file->storeAs($directory, $fileName, 'public');

                $model->files()->create([
                    'name'      => $fileName,
                    'path'      => $filePath,
                    'size'      => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
    }
}
