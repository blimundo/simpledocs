<?php

declare(strict_types=1);

use App\Enums\PermissionsEnum;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

uses(RefreshDatabase::class)->group('feature', 'http', 'controllers', 'permissions');

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can list permissions when authenticated', function () {
    Permission::factory()->count(5)->create();

    $response = $this->actingAs($this->user)
        ->getJson(route('permissions.index'));

    expect($response->status())->toBe(Response::HTTP_OK)
        ->and($response->headers->get('Content-Type'))->toContain('application/json');

    expect($response->json('data'))->toBeArray()
        ->and(count($response->json('data')))->toBe(5)
        ->and($response->json('data.0'))->toBeString();
});

it('returns unauthorized when guest tries to list permissions', function () {
    $response = $this->getJson(route('permissions.index'));

    $response->assertStatus(Response::HTTP_UNAUTHORIZED);
});

it('can search permissions when authenticated', function () {
    Permission::factory(3)->sequence(
        ['name' => PermissionsEnum::ROLES_CREATE->value],
        ['name' => PermissionsEnum::ROLES_DELETE->value],
        ['name' => PermissionsEnum::ROLES_EDIT->value],
    )->create();

    $response = $this->actingAs($this->user)
        ->getJson(route('permissions.index', ['search' => 'eDiT']));

    expect(count($response->json('data')))->toBe(1)
        ->and($response->json('data.0'))->toBe(PermissionsEnum::ROLES_EDIT->value);
});
