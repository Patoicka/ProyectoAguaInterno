<?php

namespace Src\Incident\Application\Services;

use Src\Incident\Domain\Entities\Incident;
use Src\Incident\Domain\Repositories\IncidentRepositoryInterface;

class IncidentService
{
    public function __construct(private IncidentRepositoryInterface $incidentRepository)
    {
    }

    public function all()
    {
        return $this->incidentRepository->all();
    }

    public function find(int $id)
    {
        return $this->incidentRepository->find($id);
    }

    public function create(Incident $incident)
    {
        $this->incidentRepository->create($incident);
    }

    public function update(Incident $incident)
    {
        $this->incidentRepository->update($incident);
    }

    public function delete(int $id)
    {
        $this->incidentRepository->delete($id);
    }
}