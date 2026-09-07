<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // AJOUTÉ — nécessaire pour createToken() côté API

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // AJOUTÉ HasApiTokens

    /**
     * Les attributs qui peuvent être enregistrés en base de données.
     */
    protected $fillable = [
        'name',
        'email',
        'telephone',
        'password',
        'role',
    ];

    /**
     * Les attributs qui doivent être masqués pour les tableaux de données.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être convertis.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}