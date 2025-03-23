<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionalMessage extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'client_id',
        'message',
        'phone_number',
        'status',
        'delivery_data',
        'message_id',
        'sent_at',
        'delivered_at'
    ];

    protected $casts = [
        'delivery_data' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Méthodes
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function isDelivered()
    {
        return $this->status === 'delivered';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'delivered' => 'green',
            'sent' => 'blue',
            'pending' => 'yellow',
            'failed' => 'red',
            default => 'gray'
        };
    }
}
