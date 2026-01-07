<?php

declare(strict_types=1);

use App\Actions\Disks\Disks\DeleteDiskAction;
use App\Models\Disk;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'actions', 'disks');

beforeEach(function () {
    $this->disk = Disk::factory()->create();
    $this->action = resolve(DeleteDiskAction::class);
});

it('deletes an existing disk', function () {
    $result = $this->action->handle($this->disk);

    expect($result)->toBeTrue();

    expect(Disk::count())->toBe(0)
        ->and(Disk::withTrashed()->count())->toBe(1);
});
