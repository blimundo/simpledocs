<?php

declare(strict_types=1);

use App\Actions\Disks\DiskTypes\ListDiskTypesAction;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class)->group('unit', 'actions', 'disks');

beforeEach(function () {
    $this->action = resolve(ListDiskTypesAction::class);
});

describe('listing', function () {
    it('returns empty collection when no disk types exist', function () {
        $diskTypes = $this->action->handle();

        expect($diskTypes)
            ->toBeInstanceOf(Collection::class)
            ->toBeEmpty();
    });

    it('returns all disk types without pagination', function (int $count) {
        DiskType::factory($count)->create();

        $diskTypes = $this->action->handle();

        expect($diskTypes)
            ->toHaveCount($count)
            ->each->toBeInstanceOf(DiskType::class);
    })->with([
        'single disk type' => [1],
        'few disk types' => [10],
        'many disk types' => [20],
        'large dataset' => [50],
    ]);
});

describe('filtering', function () {
    it('searches disk types by name', function () {
        DiskType::factory()->createMany([
            ['name' => 'Local Storage', 'code' => 'local'],
            ['name' => 'Amazon S3', 'code' => 's3'],
            ['name' => 'FTP Storage', 'code' => 'ftp'],
        ]);

        $diskTypes = $this->action->handle('amazon');

        expect($diskTypes)
            ->toHaveCount(1)
            ->first()->name->toBe('Amazon S3');
    });

    it('searches disk types by code', function () {
        DiskType::factory()->createMany([
            ['name' => 'Local Storage', 'code' => 'local'],
            ['name' => 'Amazon S3', 'code' => 's3'],
            ['name' => 'FTP Storage', 'code' => 'ftp'],
        ]);

        $diskTypes = $this->action->handle('ftp');

        expect($diskTypes)
            ->toHaveCount(1)
            ->first()->code->toBe('ftp');
    });
});

describe('sorting', function () {
    it('sorts disk types by name in ascending order', function () {
        DiskType::factory()->createMany([
            ['code' => 'local', 'name' => 'Local Storage'],
            ['code' => 's3', 'name' => 'Amazon S3'],
            ['code' => 'ftp', 'name' => 'FTP Storage'],
        ]);

        $diskTypes = $this->action->handle();

        expect($diskTypes->pluck('name')->toArray())
            ->toBe(['Amazon S3', 'FTP Storage', 'Local Storage']);
    });

    it('maintains stable sort order with duplicate names', function () {
        DiskType::factory()->createMany([
            ['name' => 'Storage A', 'code' => 'first'],
            ['name' => 'Storage B', 'code' => 'second'],
            ['name' => 'Storage A', 'code' => 'third'],
        ]);

        $diskTypes = $this->action->handle();

        expect($diskTypes->pluck('code'))
            ->toContain('first', 'third', 'second')
            ->and($diskTypes->first()->name)->toBe('Storage A')
            ->and($diskTypes->last()->name)->toBe('Storage B');
    });

    it('sorts names with special characters correctly', function () {
        DiskType::factory()->createMany([
            ['name' => 'Storage (Primary)'],
            ['name' => 'Storage-1'],
            ['name' => 'Storage'],
            ['name' => 'Storage_Backup'],
        ]);

        $diskTypes = $this->action->handle()->pluck('name')->toArray();

        expect($diskTypes)->toHaveCount(4)
            ->and($diskTypes)->toBe([
                'Storage',
                'Storage (Primary)',
                'Storage-1',
                'Storage_Backup',
            ]);
    });
});

describe('performance', function () {
    it('handles large datasets efficiently', function () {
        DiskType::factory(100)->create();

        $start = microtime(true);
        $diskTypes = resolve(ListDiskTypesAction::class)->handle();
        $duration = microtime(true) - $start;

        expect($diskTypes)->toHaveCount(100)
            ->and($duration)->toBeLessThan(1.0);
    });

    it('loads disk types efficiently without N+1 queries', function () {
        DiskType::factory(5)->create();

        DB::enableQueryLog();

        $this->action->handle();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        expect($queries)->toHaveCount(1);
    });
});
