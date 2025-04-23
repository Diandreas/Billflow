<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BarterItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'barter_item_id',
        'path',
        'description',
        'type',
        'order'
    ];

    /**
     * Relation avec l'article de troc auquel cette image est liée
     */
    public function barterItem()
    {
        return $this->belongsTo(BarterItem::class);
    }

    /**
     * Obtenir l'URL complète de l'image
     */
    public function getUrlAttribute()
    {
        return $this->path ? Storage::url($this->path) : null;
    }

    /**
     * Supprimer le fichier physique avant de supprimer l'enregistrement
     */
    public function delete()
    {
        if ($this->path && Storage::exists($this->path)) {
            Storage::delete($this->path);
        }

        return parent::delete();
    }
}
