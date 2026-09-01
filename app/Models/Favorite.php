<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'property_id'
    ];

    // Lien vers le bien immobilier mis en favori
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}