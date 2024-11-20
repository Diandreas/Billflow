<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'sex',
        'birth',
    ];

    protected $casts = [
        'birth' => 'date',
    ];

    public function phones()
    {
        return $this->belongsToMany(Phone::class, 'client_phone');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
