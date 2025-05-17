<?php

namespace Src\Incident\Domain\Repositories;

use Src\Incident\Domain\Entities\Incident;

interface IncidentRepositoryInterface
{
    public function all();
    public function find(int $id);
    public function create(Incident $incident);
    public function update(Incident $incident);
    public function delete(int $id);
}