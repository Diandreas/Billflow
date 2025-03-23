<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'message',
        'type',
        'status',
        'scheduled_at',
        'sent_at',
        'sms_count',
        'sms_sent',
        'sms_delivered',
        'target_segments'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'target_segments' => 'array'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(PromotionalMessage::class);
    }

    // Méthodes utilitaires
    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isScheduled()
    {
        return $this->status === 'scheduled';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getDeliveryRateAttribute()
    {
        if ($this->sms_sent === 0) {
            return 0;
        }
        return round(($this->sms_delivered / $this->sms_sent) * 100, 2);
    }
}
