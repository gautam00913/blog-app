<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/welcome', fn () => view('welcome'))->name('welcome');
Volt::route('/', 'home')->name('home');
Volt::route('/categories', 'categories')->name('categories');
Volt::route('/blog', 'blogs.index')->name('blogs.index');
Volt::route('/blog/{slug}', 'blogs.show')->name('blogs.show');
Volt::route('/about', 'about')->name('about');

Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Admin Routes
    Volt::route('dashboard/posts', 'dashboard.posts.index')->name('dashboard.posts');
    Volt::route('dashboard/posts/create', 'dashboard.posts.create')->name('dashboard.posts.create');
    Volt::route('dashboard/posts/{post}/edit', 'dashboard.posts.edit')->name('dashboard.posts.edit');
    Volt::route('dashboard/categories', 'dashboard.categories.index')->name('dashboard.categories');
    Volt::route('dashboard/comments', 'dashboard.comments.index')->name('dashboard.comments');
    Volt::route('dashboard/users', 'dashboard.users.index')->name('dashboard.users');
    Volt::route('dashboard/users/{user}/edit', 'dashboard.users.edit')->name('dashboard.users.edit');
});

require __DIR__.'/auth.php';
