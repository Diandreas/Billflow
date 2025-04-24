<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BarterImage extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'barter_id',
        'path',
        'description',
        'type'
    ];

    protected $appends = ['url'];
    
    /**
     * Relation avec le troc auquel cette image est liée
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }
    
    /**
     * Obtenir l'URL complète de l'image
     */
    public function getUrlAttribute()
    {
        if (!$this->path) {
            return null;
        }
        
        // Vérifier si le chemin est un chemin de stockage public
        if (strpos($this->path, 'public/') === 0) {
            // Enlever le préfixe 'public/' car Storage::url gère déjà cela
            $path = str_replace('public/', '', $this->path);
            return Storage::url($path);
        }
        
        // Si c'est stocké dans le disque par défaut ou un autre disque
        return Storage::url($this->path);
    }
    
    /**
     * Supprimer le fichier physique avant de supprimer l'enregistrement
     */
    public function delete()
    {
        if ($this->path) {
            // Vérifier dans quel disque de stockage se trouve l'image
            if (strpos($this->path, 'public/') === 0) {
                // C'est un fichier dans le disque public
                $path = str_replace('public/', '', $this->path);
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } else {
                // C'est un fichier dans le disque par défaut
                if (Storage::exists($this->path)) {
                    Storage::delete($this->path);
                }
            }
        }
        
        return parent::delete();
    }
} 