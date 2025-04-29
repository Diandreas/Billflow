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
        'tax_number',
        'logo_path'
    ];

    // Méthode pour récupérer les paramètres globaux
    public static function getSettings()
    {
        return self::first() ?? new self();
    }
}
