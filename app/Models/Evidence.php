<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Model
{
    use HasFactory;
    protected $fillable = [
        'comments',
        'incident_id',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function files() // imagenes de evidencia
    {
        return $this->morphMany(File::class, 'fileable');
    }
}
