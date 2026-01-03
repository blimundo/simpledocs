<?php

declare(strict_types=1);

use App\Actions\Permissions\Roles\DeleteRoleAction;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'permissions', 'roles');

beforeEach(function () {
    $this->role = Role::factory()->create();
    $this->action = resolve(DeleteRoleAction::class);
});

it('deletes a role', function () {
    $result = $this->action->handle($this->role);

    expect($result)->toBeTrue();

    expect(Role::find($this->role->id))->toBeNull()
        ->and(Role::count())->toBe(0);
});
