<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $table = 'cities';
    
    protected $fillable = [
        'name', 
        'code', 
        'state_id'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class);
    }
}
