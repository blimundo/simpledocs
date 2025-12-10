<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'users');

it('can be converted to an array', function () {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'uuid',
            'name',
            'email',
            'email_verified_at',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
});

it('can be soft deleted', function () {
    $user = User::factory()->create();

    $user->delete();

    expect($user->deleted_at)->not->toBeNull();

    expect(User::count())->toBe(0)
        ->and(User::withTrashed()->count())->toBe(1);

    $user->restore();

    expect($user->deleted_at)->toBeNull();
});

it('can be retrieved by uuid', function () {
    $user = User::factory()->create();

    $foundUser = User::findByUuid($user->uuid);

    expect($foundUser)->not->toBeNull()
        ->and($foundUser->id)->toBe($user->id);

    $notFoundUser = User::findByUuid('non-existing-uuid');

    expect($notFoundUser)->toBeNull();
});

it('can generate api tokens', function () {
    $user = User::factory()->create();

    $token = $user->createToken('Test Token');

    expect($token->plainTextToken)->toBeString();
});
