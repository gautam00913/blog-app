<?php

use App\Models\AuthorRequest;
use App\Models\User;
use App\Notifications\AuthorRequestReviewed;
use App\Notifications\NewAuthorRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

function authorRequestAdminUser(): User
{
    $role = Role::findOrCreate('admin', 'web');
    $role->givePermissionTo(Permission::findOrCreate('user.manage', 'web'));

    $user = User::factory()->create();
    $user->assignRole($role);

    return $user;
}

function seedAuthorRole(): void
{
    Role::findOrCreate('author', 'web');
}

function validMotivationLetter(): string
{
    return str_repeat('Je souhaite rejoindre votre équipe et partager mon expertise. ', 3);
}

test('a verified user can submit an author request', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.author-requests.create')
        ->set('last_degree', 'Master Informatique')
        ->set('cv', UploadedFile::fake()->create('mon-cv.pdf', 500, 'application/pdf'))
        ->set('motivation_letter', validMotivationLetter())
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('author_requests', [
        'user_id' => $user->id,
        'last_degree' => 'Master Informatique',
        'status' => 'pending',
    ]);

    Storage::disk('local')->assertExists(AuthorRequest::first()->cv_path);
});

test('the author request requires a degree, a pdf cv and a motivation letter', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.author-requests.create')
        ->set('last_degree', '')
        ->set('cv', UploadedFile::fake()->create('cv.txt', 10, 'text/plain'))
        ->set('motivation_letter', 'trop court')
        ->call('submit')
        ->assertHasErrors(['last_degree', 'cv', 'motivation_letter']);

    expect(AuthorRequest::count())->toBe(0);
});

test('a user cannot submit a second request while one is pending', function () {
    Notification::fake();
    Storage::fake('local');

    $user = User::factory()->create();
    AuthorRequest::factory()->for($user)->create();

    Volt::actingAs($user)
        ->test('dashboard.author-requests.create')
        ->set('last_degree', 'Licence')
        ->set('cv', UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'))
        ->set('motivation_letter', validMotivationLetter())
        ->call('submit');

    expect(AuthorRequest::where('user_id', $user->id)->count())->toBe(1);
});

test('admins are notified by mail and database when a request is submitted', function () {
    Notification::fake();
    Storage::fake('local');

    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('dashboard.author-requests.create')
        ->set('last_degree', 'Doctorat')
        ->set('cv', UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf'))
        ->set('motivation_letter', validMotivationLetter())
        ->call('submit')
        ->assertHasNoErrors();

    Notification::assertSentTo($admin, NewAuthorRequest::class, function (NewAuthorRequest $notification) use ($admin) {
        return in_array('mail', $notification->via($admin), true)
            && in_array('database', $notification->via($admin), true);
    });
});

test('users without the user.manage permission cannot access the author requests page', function () {
    $user = User::factory()->create();
    $user->markEmailAsVerified();

    $this->actingAs($user)
        ->get(route('dashboard.author-requests'))
        ->assertForbidden();
});

test('an admin can view the author requests page', function () {
    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $admin->markEmailAsVerified();

    $authorRequest = AuthorRequest::factory()->create();

    $this->actingAs($admin)
        ->get(route('dashboard.author-requests'))
        ->assertOk()
        ->assertSee($authorRequest->user->name);
});

test('an admin can accept a request and the user becomes an author', function () {
    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $authorRequest = AuthorRequest::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.author-requests.index')
        ->call('accept', $authorRequest->id);

    expect($authorRequest->fresh()->status)->toBe('accepted')
        ->and($authorRequest->fresh()->reviewed_at)->not->toBeNull();
    expect($authorRequest->user->fresh()->hasRole('author'))->toBeTrue();
});

test('an admin can reject a request without granting the author role', function () {
    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $authorRequest = AuthorRequest::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.author-requests.index')
        ->call('reject', $authorRequest->id);

    expect($authorRequest->fresh()->status)->toBe('rejected');
    expect($authorRequest->user->fresh()->hasRole('author'))->toBeFalse();
});

test('the applicant is notified by mail and database when the request is accepted', function () {
    Notification::fake();

    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $authorRequest = AuthorRequest::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.author-requests.index')
        ->call('accept', $authorRequest->id);

    Notification::assertSentTo($authorRequest->user, AuthorRequestReviewed::class, function (AuthorRequestReviewed $notification) use ($authorRequest) {
        return in_array('mail', $notification->via($authorRequest->user), true)
            && in_array('database', $notification->via($authorRequest->user), true)
            && $notification->authorRequest->is($authorRequest);
    });
});

test('the applicant is notified by mail and database when the request is rejected', function () {
    Notification::fake();

    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $authorRequest = AuthorRequest::factory()->create();

    Volt::actingAs($admin)
        ->test('dashboard.author-requests.index')
        ->call('reject', $authorRequest->id);

    Notification::assertSentTo($authorRequest->user, AuthorRequestReviewed::class, function (AuthorRequestReviewed $notification) use ($authorRequest) {
        return in_array('mail', $notification->via($authorRequest->user), true)
            && in_array('database', $notification->via($authorRequest->user), true)
            && $notification->authorRequest->is($authorRequest);
    });
});

test('an admin can download the cv of a request', function () {
    seedAuthorRole();
    $admin = authorRequestAdminUser();
    $admin->markEmailAsVerified();

    Storage::fake('local');
    $authorRequest = AuthorRequest::factory()->create([
        'cv_path' => 'author-requests/1/cv.pdf',
        'cv_original_name' => 'mon-cv.pdf',
    ]);
    Storage::disk('local')->put($authorRequest->cv_path, 'pdf-content');

    $this->actingAs($admin)
        ->get(route('dashboard.author-requests.cv', $authorRequest))
        ->assertDownload('mon-cv.pdf');
});
