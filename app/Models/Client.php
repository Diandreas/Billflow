<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'sex',
        'birth',
        'user_id',
    ];

    protected $casts = [
        'birth' => 'date',
    ];

    public function phones()
    {
        return $this->belongsToMany(Phone::class, 'client_phone');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function promotionalMessages()
    {
        return $this->hasMany(PromotionalMessage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBirthdayMonthAttribute()
    {
        if (!$this->birthday) {
            return null;
        }
        return date('m', strtotime($this->birthday));
    }

    public function getBirthdayDayAttribute()
    {
        if (!$this->birthday) {
            return null;
        }
        return date('d', strtotime($this->birthday));
    }

    public function isBirthdayToday()
    {
        if (!$this->birthday) {
            return false;
        }
        $birthday = date('md', strtotime($this->birthday));
        $today = date('md');
        return $birthday === $today;
    }

    public function isBirthdaySoon($days = 7)
    {
        if (!$this->birthday) {
            return false;
        }

        $birthdayMonth = $this->birthday_month;
        $birthdayDay = $this->birthday_day;

        $today = now();
        $currentYear = $today->year;

        $nextBirthday = \Carbon\Carbon::createFromDate($currentYear, $birthdayMonth, $birthdayDay);

        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }

        $daysUntilBirthday = $today->diffInDays($nextBirthday);

        return $daysUntilBirthday <= $days;
    }
}
