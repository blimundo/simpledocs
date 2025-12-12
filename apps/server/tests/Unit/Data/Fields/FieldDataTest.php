<?php

use App\Data\Fields\FieldData;
use App\Enums\FieldTypeEnum;

uses()->group('unit', 'data', 'fields');

it('can be instantiated', function () {
    $fieldData = new FieldData(
        name: 'test_field',
        type: FieldTypeEnum::STRING,
        label: 'Test Field',
        required: true,
        min: 0,
        max: 100,
    );

    expect($fieldData->name)->toBe('test_field')
        ->and($fieldData->type)->toBe(FieldTypeEnum::STRING)
        ->and($fieldData->label)->toBe('Test Field')
        ->and($fieldData->required)->toBeTrue()
        ->and($fieldData->min)->toBe(0)
        ->and($fieldData->max)->toBe(100);
});

describe('optional properties', function () {
    test('generate label if not provided', function () {
        $fieldData = new FieldData(
            name: 'generated_label_field',
            type: FieldTypeEnum::STRING
        );

        expect($fieldData->label)->toBe('Generated Label Field');
    });

    test('field are required by default', function () {
        $fieldData = new FieldData(
            name: 'optional_field',
            type: FieldTypeEnum::STRING
        );

        expect($fieldData->required)->toBeTrue();
    });

    test('min is null by default', function () {
        $fieldData = new FieldData(
            name: 'my_field',
            type: FieldTypeEnum::INTEGER
        );

        expect($fieldData->min)->toBeNull();
    });

    test('max is null by default', function () {
        $fieldData = new FieldData(
            name: 'my_field',
            type: FieldTypeEnum::INTEGER
        );

        expect($fieldData->max)->toBeNull();
    });
});

describe('validation', function () {
    test('throws error if name is empty', function () {
        new FieldData(name: '', type: FieldTypeEnum::STRING);
    })->throws(\InvalidArgumentException::class);

    test('trims whitespace from name', function () {
        $fieldData = new FieldData(
            name: '  trimmed_name  ',
            type: FieldTypeEnum::STRING
        );

        expect($fieldData->name)->toBe('trimmed_name');
    });
});

describe('rules generation', function () {
    it(
        'generates rules correctly for type string',
        function (FieldTypeEnum $type, string $laravelType, array $data, array $expectedRules) {
            $data = FieldData::from(array_merge($data, ['type' => $type]));

            expect($data->asValidationRules())->toEqualCanonicalizing(
                array_merge($expectedRules, [$laravelType])
            );
        }
    )->with('string_types', 'rules.string');

    it(
        'generates rules correctly for type boolean',
        function (FieldData $data, array $expectedRules) {
            expect($data->asValidationRules())->toEqualCanonicalizing($expectedRules);
        }
    )->with('rules.boolean');

    it(
        'generates rules correctly for type integer',
        function (FieldTypeEnum $type, string $laravelType, array $data, array $expectedRules) {
            $data = FieldData::from(array_merge($data, ['type' => $type]));

            expect($data->asValidationRules())->toEqualCanonicalizing(
                array_merge($expectedRules, [$laravelType])
            );
        }
    )->with('numeric_types', 'rules.numeric');
});

/* --- datasets --- */

dataset('string_types', [
    'string' => [FieldTypeEnum::STRING, 'string'],
    'text' => [FieldTypeEnum::TEXT, 'string'],
    'password' => [FieldTypeEnum::PASSWORD, 'string'],
    'key' => [FieldTypeEnum::KEY, 'string'],
    'email' => [FieldTypeEnum::EMAIL, 'email'],
    'url' => [FieldTypeEnum::URL, 'url'],
]);

dataset('numeric_types', [
    'integer' => [FieldTypeEnum::INTEGER, 'integer'],
]);

dataset('rules.string', [
    'required' => [
        ['name' => 'field'],
        ['required'],
    ],
    'nullable' => [
        ['name' => 'field', 'required' => false],
        ['nullable'],
    ],
    'min' => [
        ['name' => 'field', 'min' => 5],
        ['required', 'min:5'],
    ],
    'max' => [
        ['name' => 'field', 'max' => 10],
        ['required', 'max:10'],
    ],
    'min_max' => [
        ['name' => 'field', 'required' => false, 'min' => 3, 'max' => 15],
        ['nullable', 'min:3', 'max:15'],
    ],
]);

dataset('rules.boolean', [
    'required' => [
        new FieldData(name: 'field', type: FieldTypeEnum::BOOLEAN),
        ['required', 'boolean'],
    ],
    'nullable' => [
        new FieldData(name: 'field', type: FieldTypeEnum::BOOLEAN, required: false),
        ['nullable', 'boolean'],
    ],
    'ignore_min_max' => [
        new FieldData(name: 'field', type: FieldTypeEnum::BOOLEAN, min: 5, max: 10),
        ['required', 'boolean'],
    ],
]);

dataset('rules.numeric', [
    'required' => [
        ['name' => 'field'],
        ['required'],
    ],
    'nullable' => [
        ['name' => 'field', 'required' => false],
        ['nullable'],
    ],
    'min' => [
        ['name' => 'field', 'min' => 5],
        ['required',  'min:5'],
    ],
    'max' => [
        ['name' => 'field', 'max' => 10],
        ['required', 'max:10'],
    ],
    'min_max' => [
        ['name' => 'field', 'required' => false, 'min' => 3, 'max' => 15],
        ['nullable', 'min:3', 'max:15'],
    ],
]);
