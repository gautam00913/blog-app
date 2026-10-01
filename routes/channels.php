<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('notifications.{user_id}', function (User $user, int $user_id) {
    return $user->id === $user_id && $user->hasRole(['admin', 'editor']);
});