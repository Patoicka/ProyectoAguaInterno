<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'reports';

    protected $fillable = [
        'names', 
        'first_surname',
        'second_surname',
    ];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function contact()
    {
        return $this->morphOne(Contact::class, 'contactable');
    }

    public function getFullNameAttribute()
    {
        return $this->names . ' ' . $this?->first_surname . ' ' . $this?->second_surname;
    }
}
