<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incident extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'incidents';

    protected $fillable = [
        'description',
        'unique_code',
        'report_id',
        'reviewer_id',
        'location_id',
        'incident_type_id',
        'incident_status_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function incidentStatus()
    {
        return $this->belongsTo(IncidentStatus::class);
    }

    public function files() // imagenes de incidencia
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function evidence()
    {
        return $this->hasOne(Evidence::class);
    }
}
