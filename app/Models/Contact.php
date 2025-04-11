<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $table = 'contacts';

    protected $fillable = [
        'number',
        'contact_name',
        'contact_email',
        'contactable_id',
        'contactable_type',
    ];

    // Relación polimórfica con otros modelos
    public function contactable()
    {
        return $this->morphTo();
    }
}
