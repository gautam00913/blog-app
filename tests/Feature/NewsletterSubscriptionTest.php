<?php

use App\Models\Subscriber;
use Illuminate\Support\Str;

test('a valid verification link confirms the subscription and rotates the token', function () {
    $subscriber = Subscriber::factory()->unverified()->create();
    $verificationToken = $subscriber->token;

    expect(strlen($verificationToken))->toBe(50);

    $this->get(route('subscriber.verify-email', $verificationToken))
        ->assertOk()
        ->assertSee('vérifiée avec succès');

    $subscriber->refresh();

    expect($subscriber->is_verified)->toBeTrue()
        ->and($subscriber->verified_at)->not->toBeNull()
        ->and($subscriber->token)->not->toBe($verificationToken)
        ->and(strlen($subscriber->token))->toBe(32);
});

test('a verification link is single use', function () {
    $subscriber = Subscriber::factory()->unverified()->create();
    $verificationToken = $subscriber->token;

    $this->get(route('subscriber.verify-email', $verificationToken))->assertOk();
    $this->get(route('subscriber.verify-email', $verificationToken))->assertNotFound();
});

test('a verification link with a wrong token length is rejected', function () {
    $this->get(route('subscriber.verify-email', Str::random(32)))->assertNotFound();
    $this->get(route('subscriber.verify-email', Str::random(64)))->assertNotFound();
});

test('an unknown verification token is rejected', function () {
    $this->get(route('subscriber.verify-email', Str::random(50)))->assertNotFound();
});

test('an already confirmed subscription is reported as such', function () {
    $subscriber = Subscriber::factory()->verified()->create();
    $subscriber->update(['token' => Str::random(50)]);

    $this->get(route('subscriber.verify-email', $subscriber->token))
        ->assertOk()
        ->assertSee('déjà vérifiée');

    expect($subscriber->fresh()->is_verified)->toBeTrue();
});

test('an unsubscribe link deletes the subscription', function () {
    $subscriber = Subscriber::factory()->verified()->create();
    $subscriber->update(['token' => Str::random(32)]);

    $this->get(route('subscriber.unsubscribe', $subscriber->token))
        ->assertOk()
        ->assertSee('désabonnement a été effectué');

    $this->assertDatabaseMissing('subscribers', ['id' => $subscriber->id]);
});

test('an unsubscribe link with the verification token is rejected', function () {
    $subscriber = Subscriber::factory()->unverified()->create();

    $this->get(route('subscriber.unsubscribe', $subscriber->token))->assertNotFound();

    $this->assertDatabaseHas('subscribers', ['id' => $subscriber->id]);
});

test('an unknown unsubscribe token is rejected', function () {
    $this->get(route('subscriber.unsubscribe', Str::random(32)))->assertNotFound();
});
