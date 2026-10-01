<?php

namespace App\Models;

use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Str;

class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'token',
        'is_verified',
        'verified_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function scopeVerified(Builder $query)
    {
        return $query->where('is_verified', 1)->whereNotNull('verified_at');
    }

    public function routeNotificationForMail()
    {
        return $this->email;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscriber) {
            if (! $subscriber->token) {
                $subscriber->token = Str::random(50);
            }
        });

    }
}
