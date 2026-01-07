<?php

declare(strict_types=1);

use App\Models\Disk;
use App\Models\DiskType;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class)->group('unit', 'models', 'disks');

describe('model structure', function () {
    it('has correct array keys and types', function () {
        $disk = Disk::factory()->create()->refresh();

        expect($disk->toArray())
            ->toHaveKeys([
                'id',
                'disk_type_id',
                'uuid',
                'name',
                'config',
                'size',
                'used',
                'created_at',
                'updated_at',
                'deleted_at',
            ])
            ->and($disk)
            ->id->toBeInt()
            ->disk_type_id->toBeInt()
            ->uuid->toBeString()->toBeUuid()
            ->name->toBeString()
            ->config->toBeArray()
            ->size->toBeInt()
            ->used->toBeInt()
            ->created_at->toBeInstanceOf(CarbonImmutable::class)
            ->updated_at->toBeInstanceOf(CarbonImmutable::class)
            ->deleted_at->toBeNull();
    });
});

describe('casts', function () {
    it('casts config to array', function () {
        $config = ['path' => '/storage', 'bucket' => 'my-bucket'];
        $disk = Disk::factory()->create(['config' => $config]);

        expect($disk->config)
            ->toBeArray()
            ->toBe($config);
    });
});

describe('soft deletes', function () {
    it('can be soft deleted', function () {
        $disk = Disk::factory()->create();

        $disk->delete();

        expect($disk->deleted_at)
            ->not->toBeNull()
            ->toBeInstanceOf(CarbonImmutable::class);
    });

    it('is excluded from default queries after deletion', function () {
        $disk = Disk::factory()->create();

        $disk->delete();

        expect(Disk::count())->toBe(0)
            ->and(Disk::find($disk->id))->toBeNull();
    });

    it('is included in withTrashed queries', function () {
        $disk = Disk::factory()->create();

        $disk->delete();

        expect(Disk::withTrashed()->count())->toBe(1)
            ->and(Disk::withTrashed()->first()->id)->toBe($disk->id);
    });

    it('can be restored after soft deletion', function () {
        $disk = Disk::factory()->create();

        $disk->delete();
        expect($disk->deleted_at)->not->toBeNull();

        $disk->restore();

        expect($disk->deleted_at)->toBeNull()
            ->and(Disk::count())->toBe(1);
    });

    it('can be permanently deleted with forceDelete', function () {
        $disk = Disk::factory()->create();

        $disk->forceDelete();

        expect(Disk::withTrashed()->find($disk->id))->toBeNull()
            ->and(Disk::withTrashed()->count())->toBe(0);
    });
});

describe('uuid', function () {
    it('generates uuid automatically on creation', function () {
        $disk = Disk::factory()->create(['uuid' => null]);

        expect($disk->uuid)->toBeUuid();
    });

    it('finds disk by uuid', function () {
        $disk = Disk::factory()->create();

        $found = Disk::findByUuid($disk->uuid);

        expect($found)
            ->not->toBeNull()
            ->toBeInstanceOf(Disk::class)
            ->id->toBe($disk->id)
            ->uuid->toBe($disk->uuid);
    });

    it('returns null for nonexistent uuid', function () {
        expect(Disk::findByUuid(Str::uuid()->toString()))->toBeNull();
    });

    it('does not return soft deleted disks by default', function () {
        $disk = Disk::factory()->create();
        $disk->delete();

        expect(Disk::findByUuid($disk->uuid))->toBeNull();
    });
});

describe('relationships', function () {
    it('belongs to disk type', function () {
        $diskType = DiskType::factory()->create();
        $disk = Disk::factory()->create(['disk_type_id' => $diskType->id]);

        expect($disk->type)
            ->not->toBeNull()
            ->toBeInstanceOf(DiskType::class)
            ->id->toBe($diskType->id)
            ->code->toBe($diskType->code);
    });

    it('can eager load disk type', function () {
        Disk::factory(3)->create([
            'disk_type_id' => DiskType::factory()->create(),
        ]);

        $disks = Disk::with('type')->get();

        expect($disks)->toHaveCount(3)
            ->each(fn ($disk) => $disk->type->toBeInstanceOf(DiskType::class));
    });

    it('requires disk_type_id', function () {
        Disk::factory()->create(['disk_type_id' => null]);
    })->throws(Exception::class);
});
