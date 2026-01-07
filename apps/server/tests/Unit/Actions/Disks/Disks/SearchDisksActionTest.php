<?php

declare(strict_types=1);

use App\Actions\Disks\Disks\SearchDisksAction;
use App\Data\Disks\Disks\SearchDisksData;
use App\Models\Disk;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class)->group('unit', 'actions', 'disks');

beforeEach(function () {
    $this->action = resolve(SearchDisksAction::class);
});

describe('listing', function () {
    it('returns empty collection when no disks exist', function () {
        $disks = $this->action->handle(
            new SearchDisksData()
        );

        expect($disks)
            ->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
            ->toBeEmpty();
    });

    it('returns all disks with pagination', function () {
        Disk::factory(20)->create();
        $disks = $this->action->handle(
            new SearchDisksData()
        );

        expect($disks)
            ->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
            ->toHaveCount(15)
            ->first()->toBeInstanceOf(Disk::class);
    });
});

describe('filtering', function () {
    it('filters disks by disk type code', function () {
        $s3Type = DiskType::factory()->create(['code' => 's3', 'name' => 'Amazon S3']);
        $localType = DiskType::factory()->create(['code' => 'local', 'name' => 'Local Storage']);

        Disk::factory()->createMany([
            ['name' => 'S3 Disk 1', 'disk_type_id' => $s3Type->id],
            ['name' => 'Local Disk 1', 'disk_type_id' => $localType->id],
            ['name' => 'S3 Disk 2', 'disk_type_id' => $s3Type->id],
        ]);

        $disks = $this->action->handle(
            new SearchDisksData(type: 's3')
        );

        expect($disks)->toHaveCount(2);
    });

    it('filters disks by partial name match', function () {
        Disk::factory()->createMany([
            ['name' => 'Backup Storage'],
            ['name' => 'Primary Storage'],
            ['name' => 'Archive Storage'],
        ]);

        $disks = $this->action->handle(
            new SearchDisksData(name: 'PRIMA')
        );

        expect($disks)
            ->toHaveCount(1)
            ->first()->name->toBe('Primary Storage');
    });

    it('returns empty collection when no disks match the filter', function () {
        $localType = DiskType::factory()->create(['code' => 'local']);

        Disk::factory()->createMany([
            ['name' => 'Local Disk 1', 'disk_type_id' => $localType->id],
            ['name' => 'Local Disk 2', 'disk_type_id' => $localType->id],
        ]);

        $disks = $this->action->handle(
            new SearchDisksData(type: 's3')
        );

        expect($disks)
            ->toBeInstanceOf(Illuminate\Pagination\LengthAwarePaginator::class)
            ->toBeEmpty();
    });
});

describe('sorting', function () {
    it('sorts disks by name in ascending order', function () {
        Disk::factory()->createMany([
            ['name' => 'Local Storage'],
            ['name' => 'Amazon S3'],
            ['name' => 'FTP Storage'],
        ]);

        $disks = $this->action->handle(
            new SearchDisksData()
        );

        expect($disks->pluck('name')->toArray())
            ->toBe(['Amazon S3', 'FTP Storage', 'Local Storage'])
            ->and($disks)->toHaveCount(3);
    });

    it('sorts names with special characters correctly', function () {
        Disk::factory()->createMany([
            ['name' => 'Storage (Primary)'],
            ['name' => 'Storage-1'],
            ['name' => 'Storage'],
            ['name' => 'Storage_Backup'],
        ]);

        $disks = $this->action->handle(
            new SearchDisksData()
        );
        $names = $disks->pluck('name')->toArray();

        expect($names)->toHaveCount(4)
            ->and($names)->toBe([
                'Storage',
                'Storage (Primary)',
                'Storage-1',
                'Storage_Backup',
            ]);
    });
});

describe('performance', function () {
    it('loads disk efficiently without N+1 queries', function () {
        Disk::factory(5)->create();

        DB::enableQueryLog();

        $disks = $this->action->handle(
            new SearchDisksData()
        );

        $disks->map(fn (Disk $disk) => $disk->type);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        expect($queries)->toHaveCount(3);
    });
});
