<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'status',
        'description',
        'location',
        'user_id',
    ];

    /**
     * Relation avec la livraison
     */
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    /**
     * Relation avec l'utilisateur qui a mis à jour le statut
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesseur pour formater le statut
     */
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            'en_préparation' => 'En préparation',
            'prêt_pour_expédition' => 'Prêt pour expédition',
            'en_transit' => 'En transit',
            'livré' => 'Livré',
            'annulé' => 'Annulé',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Vérifie si ce log représente un statut final (livré ou annulé)
     */
    public function isFinalStatus()
    {
        return in_array($this->status, ['livré', 'annulé']);
    }
} 