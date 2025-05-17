<?php

namespace Src\Incident\Domain\Entities;

class Incident
{
    public function __construct(
        private int $id,
        private string $description,
        private string $uniqueCode,
        // private int $reportId,
        // private ?int $reviewerId,
        private int $locationId,
        private int $incidentTypeId,
        private int $incidentStatusId,
        // private \DateTime $createdAt,
        // private \DateTime $updatedAt,
        private array $location,
        private array $incidentType,
        private array $incidentStatus
    ) {
    }

    // public function getId(): int
    // {
    //     return $this->id;
    // }

    // public function getDescription(): string
    // {
    //     return $this->description;
    // }

    // public function getUniqueCode(): string
    // {
    //     return $this->uniqueCode;
    // }

    // public function getReportId(): int
    // {
    //     return $this->reportId;
    // }

    // public function getReviewerId(): ?int
    // {
    //     return $this->reviewerId;
    // }

    public function getLocationId(): int
    {
        return $this->locationId;
    }

    public function getIncidentTypeId(): int
    {
        return $this->incidentTypeId;
    }

    public function getIncidentStatusId(): int
    {
        return $this->incidentStatusId;
    }

    // public function getCreatedAt(): \DateTime
    // {
    //     return $this->createdAt;
    // }

    // public function getUpdatedAt(): \DateTime
    // {
    //     return $this->updatedAt;
    // }

    public function getLocation(): array
    {
        return $this->location;
    }

    public function getIncidentType(): array
    {
        return $this->incidentType;
    }

    public function getIncidentStatus(): array
    {
        return $this->incidentStatus;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'unique_code' => $this->uniqueCode,
            // 'report_id' => $this->reportId,
            // 'reviewer_id' => $this->reviewerId,
            // 'location_id' => $this->locationId,
            // 'incident_type_id' => $this->incidentTypeId,
            // 'incident_status_id' => $this->incidentStatusId,
            // 'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            // 'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'location' => $this->location,
            'incident_type' => $this->incidentType,
            'incident_status' => $this->incidentStatus
        ];
    }
}