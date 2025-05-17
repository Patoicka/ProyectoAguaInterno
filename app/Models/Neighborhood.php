<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;
    protected $table = 'neighborhoods';

    protected $fillable = [
        'name', 
        'code',
        'postal_code', 
        'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
