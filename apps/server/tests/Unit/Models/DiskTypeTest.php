<?php

declare(strict_types=1);

use App\Models\Disk;
use App\Models\DiskType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('unit', 'models', 'disks');

describe('model structure', function () {
    it('has correct array keys and types', function () {
        $diskType = DiskType::factory()->create()->refresh();

        expect($diskType->toArray())
            ->toHaveKeys([
                'id',
                'code',
                'name',
                'driver',
                'fields',
            ])
            ->not->toHaveKeys(['created_at', 'updated_at', 'deleted_at'])
            ->and($diskType)
            ->id->toBeInt()
            ->code->toBeString()
            ->name->toBeString()
            ->driver->toBeString()
            ->fields->toBeArray();
    });
});

describe('casts', function () {
    it('casts fields to array', function () {
        $fields = ['key1' => 'value1', 'key2' => 'value2'];
        $diskType = DiskType::factory()->create(['fields' => $fields]);

        expect($diskType->fields)
            ->toBeArray()
            ->toBe($fields);
    });
});

describe('code field', function () {
    it('finds disk type by code', function () {
        $diskType = DiskType::factory()->create();

        $found = DiskType::findByCode($diskType->code);

        expect($found)
            ->not->toBeNull()
            ->toBeInstanceOf(DiskType::class)
            ->id->toBe($diskType->id)
            ->code->toBe($diskType->code);
    });

    it('returns null for nonexistent code', function () {
        expect(DiskType::findByCode('non-existing-code'))->toBeNull();
    });

    it('finds disk type by code with findByCodeOrFail', function () {
        $diskType = DiskType::factory()->create();

        $found = DiskType::findByCodeOrFail($diskType->code);

        expect($found)
            ->toBeInstanceOf(DiskType::class)
            ->id->toBe($diskType->id)
            ->code->toBe($diskType->code);
    });

    it('throws exception for nonexistent code with findByCodeOrFail', function () {
        DiskType::findByCodeOrFail('non-existing-code');
    })->throws(Illuminate\Database\Eloquent\ModelNotFoundException::class);
});

describe('relationships', function () {
    it('has many disks', function () {
        $diskType = DiskType::factory()->create();
        Disk::factory(3)->create(['disk_type_id' => $diskType->id]);

        expect($diskType->disks)
            ->toBeInstanceOf(Illuminate\Support\Collection::class)
            ->and($diskType->disks->count())->toBe(3)
            ->and($diskType->disks)
            ->each->toBeInstanceOf(Disk::class);
    });
});

/**
 * No need to cover all possible field types here,
 * since FieldCollectionData::asValidationRules() and
 * FieldCollectionData::asForm() are already thoroughly
 * tested in their dedicated unit tests.
 *
 * @see tests/Unit/Data/Fields/FieldCollectionDataTest.php
 */
describe('fields', function () {
    it('returns validation rules as array with correct structure', function () {
        $diskType = DiskType::factory()->create();
        $rules = $diskType->getValidationRules();

        expect($rules)
            ->toBeArray()
            ->not->toBeEmpty()
            ->and($rules)->toHaveKey('path')
            ->and($rules)->toHaveKey('path.0', 'required')
            ->and($rules)->toHaveKey('path.1', 'string')
            ->and($rules)->toHaveKey('path.2', 'max:500');
    });

    it('returns form representation with correct schema', function () {
        $diskType = DiskType::factory()->create();
        $form = $diskType->getFormRepresentation();

        expect($form)
            ->toBeArray()
            ->not->toBeEmpty()
            ->and($form)->toHaveKey('0.name', 'path')
            ->and($form)->toHaveKey('0.widget', 'text')
            ->and($form)->toHaveKey('0.label', 'Storage Path')
            ->and($form)->toHaveKey('0.rules')
            ->and($form['0']['rules'])->toBeArray()
            ->and($form['0']['rules'])->toHaveKeys(['required', 'maxlength']);
    });
});
