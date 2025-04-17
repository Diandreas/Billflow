<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorEquipment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'shop_id',
        'name',
        'type',
        'serial_number',
        'quantity',
        'assigned_date',
        'assigned_by',
        'condition',
        'notes',
        'status',
        'returned_date',
        'return_condition',
        'return_notes',
        'returned_to',
    ];

    /**
     * Les attributs à caster.
     *
     * @var array
     */
    protected $casts = [
        'assigned_date' => 'date',
        'returned_date' => 'date',
        'quantity' => 'integer',
    ];

    /**
     * Relation avec l'utilisateur (vendeur) qui a reçu l'équipement.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation avec la boutique associée à cet équipement.
     */
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    /**
     * Relation avec l'utilisateur qui a assigné l'équipement.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Relation avec l'utilisateur qui a reçu l'équipement retourné.
     */
    public function returnedTo()
    {
        return $this->belongsTo(User::class, 'returned_to');
    }

    /**
     * Scope pour filtrer les équipements assignés.
     */
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    /**
     * Scope pour filtrer les équipements retournés.
     */
    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    /**
     * Vérifie si l'équipement est actuellement assigné
     */
    public function isAssigned()
    {
        return $this->status === 'assigned';
    }

    /**
     * Vérifie si l'équipement a été retourné
     */
    public function isReturned()
    {
        return $this->status === 'returned';
    }

    /**
     * Obtenir le nom formaté de l'état
     */
    public function getFormattedConditionAttribute()
    {
        $conditions = [
            'neuf' => 'Neuf',
            'bon' => 'Bon état',
            'moyen' => 'État moyen',
            'mauvais' => 'Mauvais état',
        ];

        return $conditions[$this->condition] ?? $this->condition;
    }
} 