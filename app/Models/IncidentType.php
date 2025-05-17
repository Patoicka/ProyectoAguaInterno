<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    use HasFactory;
    protected $table = 'incident_types';

    protected $fillable = [
        'name', 
        'description',
        'status'
    ];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
