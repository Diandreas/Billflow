<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_number',
        'bill_id',
        'barter_id',
        'client_id',
        'delivery_type',
        'status',
        'shipping_address',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'delivery_fee',
        'estimated_delivery_date',
        'actual_delivery_date',
        'delivery_agent_id',
        'notes',
        'requires_signature',
        'signature_path',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
        'estimated_delivery_date' => 'datetime',
        'actual_delivery_date' => 'datetime',
        'requires_signature' => 'boolean',
    ];

    /**
     * Génère un numéro de suivi unique
     */
    public static function generateTrackingNumber()
    {
        return 'LIV-' . date('YmdHis') . '-' . rand(1000, 9999);
    }

    /**
     * Relation avec la facture liée
     */
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    /**
     * Relation avec le troc lié
     */
    public function barter()
    {
        return $this->belongsTo(Barter::class);
    }

    /**
     * Relation avec le client destinataire
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'agent de livraison
     */
    public function deliveryAgent()
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }

    /**
     * Relation avec l'historique des statuts
     */
    public function statusLogs()
    {
        return $this->hasMany(DeliveryStatusLog::class);
    }

    /**
     * Relation avec le dernier statut de livraison
     */
    public function lastStatusLog()
    {
        return $this->hasOne(DeliveryStatusLog::class)->latest();
    }

    /**
     * Mettre à jour le statut de la livraison
     */
    public function updateStatus($status, $description = null, $location = null, $userId = null)
    {
        // Mettre à jour le statut dans le modèle
        $this->status = $status;
        $this->save();
        
        // Créer une entrée dans l'historique des statuts
        return $this->statusLogs()->create([
            'status' => $status,
            'description' => $description,
            'location' => $location,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Marquer la livraison comme préparée
     */
    public function markAsReady($userId = null)
    {
        return $this->updateStatus('prêt_pour_expédition', 'Colis préparé et prêt pour l\'expédition', null, $userId);
    }

    /**
     * Marquer la livraison comme en transit
     */
    public function markAsInTransit($location = null, $userId = null)
    {
        return $this->updateStatus('en_transit', 'Colis en transit vers la destination', $location, $userId);
    }

    /**
     * Marquer la livraison comme livrée
     */
    public function markAsDelivered($userId = null)
    {
        $this->actual_delivery_date = now();
        $this->save();
        
        return $this->updateStatus('livré', 'Colis livré au destinataire', null, $userId);
    }

    /**
     * Marquer la livraison comme annulée
     */
    public function markAsCancelled($reason = null, $userId = null)
    {
        return $this->updateStatus('annulé', $reason ?? 'Livraison annulée', null, $userId);
    }

    /**
     * Assigner un agent de livraison
     */
    public function assignDeliveryAgent($agentId)
    {
        $this->delivery_agent_id = $agentId;
        $this->save();
        
        // Ajouter une entrée dans l'historique
        $agent = User::find($agentId);
        $this->statusLogs()->create([
            'status' => $this->status,
            'description' => 'Agent de livraison assigné: ' . $agent->name,
            'user_id' => auth()->id(),
        ]);
        
        return $this;
    }

    /**
     * Scope pour les livraisons en cours (non livrées et non annulées)
     */
    public function scopeInProgress($query)
    {
        return $query->whereNotIn('status', ['livré', 'annulé']);
    }

    /**
     * Scope pour les livraisons livrées
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'livré');
    }

    /**
     * Scope pour les livraisons annulées
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'annulé');
    }

    /**
     * Scope pour les livraisons d'un agent spécifique
     */
    public function scopeForAgent($query, $agentId)
    {
        return $query->where('delivery_agent_id', $agentId);
    }

    /**
     * Récupère le temps restant jusqu'à la livraison estimée
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->estimated_delivery_date) {
            return null;
        }
        
        return now()->diffForHumans($this->estimated_delivery_date, ['parts' => 2]);
    }

    /**
     * Vérifie si la livraison est en retard
     */
    public function getIsLateAttribute()
    {
        if (!$this->estimated_delivery_date || in_array($this->status, ['livré', 'annulé'])) {
            return false;
        }
        
        return now()->isAfter($this->estimated_delivery_date);
    }
} 