<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'price_paid',
        'status',
        'sms_remaining',
        'sms_personal_remaining',
        'campaigns_used',
        'transaction_reference',
        'payment_data'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'payment_data' => 'array'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    // Méthodes
    public function isActive()
    {
        return $this->status === 'active' && now()->between($this->starts_at, $this->ends_at);
    }

    public function isExpired()
    {
        return now()->greaterThan($this->ends_at);
    }

    public function getFormattedPricePaidAttribute()
    {
        return number_format($this->price_paid, 0, ',', ' ') . ' FCFA';
    }

    public function getSmsUsagePercentAttribute()
    {
        $plan = $this->plan;
        if (!$plan) return 0;
        $total = $plan->sms_quota;
        if ($total === 0) return 0;
        $used = $total - $this->sms_remaining;
        return round(($used / $total) * 100, 1);
    }

    public function getPersonalSmsUsagePercentAttribute()
    {
        $plan = $this->plan;
        if (!$plan) return 0;
        $total = $plan->sms_personal_quota;
        if ($total === 0) return 0;
        $used = $total - $this->sms_personal_remaining;
        return round(($used / $total) * 100, 1);
    }

    public function getCampaignsUsagePercentAttribute()
    {
        $plan = $this->plan;
        if (!$plan) return 0;
        $total = $plan->campaigns_per_cycle;
        if ($total === 0) return 0;
        return round(($this->campaigns_used / $total) * 100, 1);
    }
}
