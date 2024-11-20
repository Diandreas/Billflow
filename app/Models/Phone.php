<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = [
        'number',
    ];

    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_phone');
    }
}
