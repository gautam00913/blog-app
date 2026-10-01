<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Public routes
Route::get('/welcome', fn () => view('welcome'))->name('welcome');
Volt::route('/', 'home')->name('home');
Volt::route('/categories', 'categories')->name('categories');
Volt::route('/blog', 'blogs.index')->name('blogs.index');
Volt::route('/blog/{slug}', 'blogs.show')->name('blogs.show');
Volt::route('/about', 'about')->name('about');
Volt::route('/mentions-legales', 'mentions-legales')->name('mentions-legales');
Volt::route('/confidentialite', 'confidentialite')->name('confidentialite');
Volt::route('/cgu', 'cgu')->name('cgu');
Volt::route('/subscriber/{token}', 'newsletter.verify-email')->name('subscriber.verify-email');
Volt::route('/subscriber/{token}/unsubscribe', 'newsletter.unsubscribe')->name('subscriber.unsubscribe');

// Dashboard - all authenticated & verified users
Volt::route('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Settings - all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Posts management - authors, editors, admins
    Volt::route('dashboard/posts', 'dashboard.posts.index')->can('post.create')->name('dashboard.posts');
    Volt::route('dashboard/posts/create', 'dashboard.posts.create')->can('post.create')->name('dashboard.posts.create');
    Volt::route('dashboard/posts/{post}/edit', 'dashboard.posts.edit')->can('post.edit')->name('dashboard.posts.edit');

    // Categories management - admin only
    Volt::route('dashboard/categories', 'dashboard.categories.index')->can('category.manage')->name('dashboard.categories');

    // Comments management - admin, editor
    Volt::route('dashboard/comments', 'dashboard.comments.index')->can('comment.manage')->name('dashboard.comments');
});

// User management - admin only
Route::middleware(['auth', 'verified'])->can('user.manage')->group(function () {
    Volt::route('dashboard/users', 'dashboard.users.index')->name('dashboard.users');
    Volt::route('dashboard/users/create', 'dashboard.users.create')->name('dashboard.users.create');
    Volt::route('dashboard/users/{user}/edit', 'dashboard.users.edit')->name('dashboard.users.edit');
});

require __DIR__.'/auth.php';
