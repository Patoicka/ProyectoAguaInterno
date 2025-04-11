<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table = 'locations';

    protected $fillable = [
        'street',
        'interior_number',
        'exterior_number',
        'additional',
        'references',
        'neighborhood_id'
    ];

    // relacion con colonias
    public function neighborhood()
    {
        return $this->belongsTo(Neighborhood::class);
    }

    public function incident()
    {
        return $this->hasOne(Incident::class);
    }

    public function getFullAddressAttribute()
    {
        return [
            'address'       => ($this->street ?? 'Sin calle') . ' ' . ($this->interior_number ?? 'SN') . ' ' . ($this->exterior_number ?? 'SN'),
            'additional'    => $this->additional,
            'references'    => $this->references,
            'neighborhood'  => $this->neighborhood?->name ?? 'Sin colonia',
            'city'          => $this->neighborhood?->city?->name ?? 'Sin municipio',
            'state'         => $this->neighborhood?->municipality?->state?->name ?? 'Sin estado',
            'postalCode'    => $this->neighborhood->postal_code ?? 'SN',
        ];
    }
}
