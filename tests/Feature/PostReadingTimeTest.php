<?php

use App\Models\Post;
use Livewire\Volt\Volt;

test('reading time counts words and ignores html markup', function () {
    $post = Post::factory()->published()->create([
        'body' => str_repeat('<p>mot un deux trois</p>', 100),
    ]);

    expect($post->readingMinutes())->toBe(2);
});

test('reading time never drops below one minute', function () {
    $post = Post::factory()->published()->create(['body' => '<p>Un seul mot</p>']);

    expect($post->readingMinutes())->toBe(1);
});

test('the post page displays the reading time without counting markup', function () {
    $post = Post::factory()->published()->create([
        'body' => str_repeat('<p>mot un deux trois</p>', 100),
    ]);

    Volt::test('blogs.show', ['slug' => $post->slug])
        ->assertSee('2 min de lecture');
});
