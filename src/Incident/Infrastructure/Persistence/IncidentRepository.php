<?php

namespace Src\Incident\Infrastructure\Persistence;

use Src\Incident\Domain\Entities\Incident;
use Src\Incident\Domain\Repositories\IncidentRepositoryInterface;
use App\Models\Incident as IncidentModel;

class IncidentRepository implements IncidentRepositoryInterface
{
    public function all()
    {
        $incidents = IncidentModel::select([
            'id',
            'description',
            'unique_code',
            'location_id',
            'incident_type_id',
            'incident_status_id',
        ])->with([
            'location' => function ($query) {
                $query->select(['id', 'lat', 'lng', 'neighborhood_id'])
                ->with(['neighborhood' => function ($query) {
                    $query->select(['id', 'city_id'])
                    ->with(['city' => function ($query) {
                        $query->select(['id', 'name']);
                    }]);
                }]);
            },
            'incidentType' => function ($query) {
                $query->select(['id', 'name']);
            },
            'incidentStatus' => function ($query) {
                $query->select(['id', 'name']);
            },
        ])->get();
        $incidents = $incidents->map(function ($incident) {
            return new Incident(
                $incident->id,
                $incident->description,
                $incident->unique_code,
                // $incident->report_id,
                // $incident->reviewer_id,
                $incident->location_id,
                $incident->incident_type_id,
                $incident->incident_status_id,
                // $incident->created_at,
                // $incident->updated_at
                $incident->location->toArray(),
                $incident->incidentType->toArray(),
                $incident->incidentStatus->toArray()
            );
        });

        return $incidents;
    }

    public function find(int $id)
    {

        $incident = IncidentModel::select([
            'id',
            'description',
            'unique_code',
            'location_id',
            'incident_type_id',
            'incident_status_id'
        ])->with([
            'location' => function ($query) {
                $query->select(['id', 'lat', 'lng', 'neighborhood_id'])
                ->with(['neighborhood' => function ($query) {
                    $query->select(['id', 'city_id'])
                    ->with(['city' => function ($query) {
                        $query->select(['id', 'name']);
                    }]);
                }]);
            },
            'incidentType' => function ($query) {
                $query->select(['id', 'name']);
            },
            'incidentStatus' => function ($query) {
                $query->select(['id', 'name']);
            },
        ])->find($id);
        if (!$incident) {
            throw new \Exception('Incident not found');
        }
        return new Incident(
            $incident->id,
            $incident->description,
            $incident->unique_code,
            // $incident->report_id,
            // $incident->reviewer_id,
            $incident->location_id,
            $incident->incident_type_id,
            $incident->incident_status_id,
            // $incident->created_at,
            // $incident->updated_at,
            $incident->location->toArray(),
            $incident->incidentType->toArray(),
            $incident->incidentStatus->toArray()
        );
    }

    public function create(Incident $incident)
    {
        $incidentModel = new IncidentModel();
        $incidentModel->description = $incident->getDescription();
        $incidentModel->unique_code = $incident->getUniqueCode();
        $incidentModel->report_id = $incident->getReportId();
        $incidentModel->reviewer_id = $incident->getReviewerId();
        $incidentModel->location_id = $incident->getLocationId();
        $incidentModel->incident_type_id = $incident->getIncidentTypeId();
        $incidentModel->incident_status_id = $incident->getIncidentStatusId();
        $incidentModel->created_at = $incident->getCreatedAt();
        $incidentModel->updated_at = $incident->getUpdatedAt();
        $incidentModel->save();
    }

    public function update(Incident $incident)
    {
        $incidentModel = IncidentModel::find($incident->getId());
        $incidentModel->description = $incident->getDescription();
        $incidentModel->unique_code = $incident->getUniqueCode();
        $incidentModel->report_id = $incident->getReportId();
        $incidentModel->reviewer_id = $incident->getReviewerId();
        $incidentModel->location_id = $incident->getLocationId();
        $incidentModel->incident_type_id = $incident->getIncidentTypeId();
        $incidentModel->incident_status_id = $incident->getIncidentStatusId();
        $incidentModel->created_at = $incident->getCreatedAt();
        $incidentModel->updated_at = $incident->getUpdatedAt();
        $incidentModel->save();
    }

    public function delete(int $id)
    {
        $incidentModel = IncidentModel::find($id);
        $incidentModel->delete();
    }
}
