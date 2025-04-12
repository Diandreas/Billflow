<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Les plans d'abonnement associés à cette caractéristique
     */
    public function subscriptionPlans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'subscription_plan_feature')
            ->withPivot('value')
            ->withTimestamps();
    }
} 