<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarterImage extends Model
{
    protected $fillable = [
        'barter_id',
        'path',
        'description',
        'type'
    ];

    /**
     * Relation avec le troc auquel cette image est liée
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }
} 