<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'billing_cycle',
        'max_clients',
        'campaigns_per_cycle',
        'sms_quota',
        'sms_personal_quota',
        'sms_rollover_percent',
        'is_active',
    ];

    /**
     * Les caractéristiques associées à ce plan d'abonnement
     */
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'subscription_plan_feature')
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Les abonnements liés à ce plan
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    // Méthodes
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    public function getCycleTextAttribute()
    {
        return $this->billing_cycle === 'monthly' ? 'Mensuel' : 'Annuel';
    }
}
