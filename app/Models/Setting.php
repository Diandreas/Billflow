<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'phone',
        'email',
        'website',
        'siret',
        'logo_path'
    ];

    // Méthode pour récupérer les paramètres globaux
}
