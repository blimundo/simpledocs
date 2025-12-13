<?php

use App\Data\Fields\FieldCollectionData;
use App\Enums\FieldTypeEnum;
use App\Data\Fields\FieldData;

uses()->group('unit', 'data', 'fields');

it('can create an empty FieldCollectionData', function () {
    $fieldCollection = new FieldCollectionData();

    expect($fieldCollection->fields)->toBeInstanceOf(\Illuminate\Support\Collection::class)
        ->and($fieldCollection->fields)->toBeEmpty();
});

it('can create a FieldCollectionData with an array of fields', function () {
    $fields = [
        ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
        ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
    ];

    $fieldCollection = new FieldCollectionData($fields);
    $expectedFirstField = [
        'name' => 'field1',
        'type' => FieldTypeEnum::STRING->value,
        'required' => true,
        'min' => null,
        'max' => null,
        'label' => 'Field1',
    ];

    expect($fieldCollection->fields)->toHaveCount(2)
        ->and($fieldCollection->fields->first())->toBeInstanceOf(FieldData::class)
        ->and($fieldCollection->fields->first()->toArray())->toEqual($expectedFirstField);
    $fieldCollection = FieldCollectionData::from(['fields' => $fields]);

    expect($fieldCollection->fields)->toHaveCount(2)
        ->and($fieldCollection->fields->first())->toBeInstanceOf(FieldData::class)
        ->and($fieldCollection->fields->first()->toArray())->toEqual($expectedFirstField);
});

it('can create a FieldCollectionData with a Collection of fields', function () {
    $fields = collect([
        ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
        ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
    ]);

    $fieldCollection = new FieldCollectionData($fields);
    $expectedFirstField = [
        'name' => 'field1',
        'type' => FieldTypeEnum::STRING->value,
        'required' => true,
        'min' => null,
        'max' => null,
        'label' => 'Field1',
    ];

    expect($fieldCollection->fields)->toHaveCount(2)
        ->and($fieldCollection->fields->first())->toBeInstanceOf(FieldData::class)
        ->and($fieldCollection->fields->first()->toArray())->toEqual($expectedFirstField);

    $fieldCollection = FieldCollectionData::from(['fields' => $fields]);

    expect($fieldCollection->fields)->toHaveCount(2)
        ->and($fieldCollection->fields->first())->toBeInstanceOf(FieldData::class)
        ->and($fieldCollection->fields->first()->toArray())->toEqual($expectedFirstField);
});

it('generates validation rules for each field in the collection', function () {
    $fields = [
        ['name' => 'username', 'type' => FieldTypeEnum::STRING, 'min' => 3, 'max' => 20],
        ['name' => 'password', 'type' => FieldTypeEnum::PASSWORD, 'required' => true],
        ['name' => 'email', 'type' => FieldTypeEnum::EMAIL, 'required' => true, 'max' => 100],
        ['name' => 'age', 'type' => FieldTypeEnum::INTEGER, 'required' => false, 'min' => 0, 'max' => 120],
        ['name' => 'bio', 'type' => FieldTypeEnum::TEXT, 'required' => false, 'max' => 500],
        ['name' => 'accept_terms', 'type' => FieldTypeEnum::BOOLEAN, 'required' => true],
    ];

    $expectedRules = [
        'username' => ['required', 'string', 'min:3', 'max:20'],
        'password' => ['required', 'string'],
        'email' => ['required', 'email', 'max:100'],
        'age' => ['nullable', 'integer', 'min:0', 'max:120'],
        'bio' => ['nullable', 'string', 'max:500'],
        'accept_terms' => ['required', 'boolean'],
    ];

    $fieldCollection = new FieldCollectionData($fields);

    expect($fieldCollection->asValidationRules())->toEqual($expectedRules);
});

it('generates validation rules for an empty field collection', function () {
    $fieldCollection = new FieldCollectionData();

    expect($fieldCollection->asValidationRules())->toEqual([]);
});

it('generates form representation for each field in the collection', function () {
    $fields = [
        ['name' => 'username', 'type' => FieldTypeEnum::STRING, 'min' => 3, 'max' => 20],
        ['name' => 'password', 'type' => FieldTypeEnum::PASSWORD, 'required' => true],
        ['name' => 'email', 'type' => FieldTypeEnum::EMAIL, 'required' => true, 'max' => 100],
        ['name' => 'age', 'type' => FieldTypeEnum::INTEGER, 'required' => false, 'min' => 0, 'max' => 120],
        ['name' => 'bio', 'type' => FieldTypeEnum::TEXT, 'required' => false, 'max' => 500],
        ['name' => 'accept_terms', 'type' => FieldTypeEnum::BOOLEAN, 'required' => true],
    ];

    $expectedFormRepresentation = [
        ['name' => 'username', 'type' => FieldTypeEnum::STRING->value, 'required' => true, 'min' => 3, 'max' => 20, 'label' => 'Username'],
        ['name' => 'password', 'type' => FieldTypeEnum::PASSWORD->value, 'required' => true, 'min' => null, 'max' => null, 'label' => 'Password'],
        ['name' => 'email', 'type' => FieldTypeEnum::EMAIL->value, 'required' => true, 'min' => null, 'max' => 100, 'label' => 'Email'],
        ['name' => 'age', 'type' => FieldTypeEnum::INTEGER->value, 'required' => false, 'min' => 0, 'max' => 120, 'label' => 'Age'],
        ['name' => 'bio', 'type' => FieldTypeEnum::TEXT->value, 'required' => false, 'min' => null, 'max' => 500, 'label' => 'Bio'],
        ['name' => 'accept_terms', 'type' => FieldTypeEnum::BOOLEAN->value, 'required' => true, 'min' => null, 'max' => null, 'label' => 'Accept Terms'],
    ];

    $fieldCollection = new FieldCollectionData($fields);

    $formRepresentation = $fieldCollection->fields->map(fn(FieldData $field) => $field->toArray())->toArray();

    expect($formRepresentation)->toEqual($expectedFormRepresentation);
});
