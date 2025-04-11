<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentStatus extends Model
{
    use HasFactory;
    protected $table = 'incident_statuses';
    
    protected $fillable = ['name', 'class'];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
