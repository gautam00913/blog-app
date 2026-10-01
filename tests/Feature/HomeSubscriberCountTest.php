<?php

use App\Models\Subscriber;
use Livewire\Volt\Volt;

test('the home page only counts verified newsletter subscribers', function () {
    Subscriber::create([
        'email' => 'verified@example.com',
        'is_verified' => true,
        'verified_at' => now(),
    ]);

    Subscriber::create([
        'email' => 'pending@example.com',
        'is_verified' => false,
        'verified_at' => null,
    ]);

    Volt::test('home')->assertSet('subscribersCount', 1);
});

test('the home page shows no subscriber count when nobody has verified', function () {
    Subscriber::create([
        'email' => 'pending@example.com',
        'is_verified' => false,
        'verified_at' => null,
    ]);

    Volt::test('home')->assertSet('subscribersCount', 0);
});
