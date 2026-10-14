<?php

use App\Models\Post;

test('featuredImageUrl builds the public storage url for a relative path', function () {
    $post = Post::factory()->create(['featured_image' => 'posts/couv.jpg']);

    expect($post->featuredImageUrl())->toBe(asset('storage/posts/couv.jpg'));
});

test('featuredImageUrl returns the stored url for an external image', function () {
    $post = Post::factory()->create(['featured_image' => 'https://example.com/couv.jpg']);

    expect($post->featuredImageUrl())->toBe('https://example.com/couv.jpg');
});

test('featuredImageUrl is null without an image', function () {
    $post = Post::factory()->create(['featured_image' => null]);

    expect($post->featuredImageUrl())->toBeNull();
});
