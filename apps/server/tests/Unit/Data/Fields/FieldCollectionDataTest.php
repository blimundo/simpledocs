<?php

declare(strict_types=1);

use App\Data\Fields\FieldCollectionData;
use App\Data\Fields\FieldData;
use App\Enums\FieldTypeEnum;
use Illuminate\Support\Collection;

uses()->group('unit', 'data', 'fields');

describe('instantiation', function () {
    it('creates an empty collection by default', function () {
        $fieldCollection = new FieldCollectionData();

        expect($fieldCollection->fields)
            ->toBeInstanceOf(Collection::class)
            ->toBeEmpty();
    });

    it('creates collection from array of field definitions', function () {
        $fields = [
            ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
            ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
        ];

        $fieldCollection = new FieldCollectionData($fields);

        expect($fieldCollection->fields)
            ->toHaveCount(2)
            ->each->toBeInstanceOf(FieldData::class)
            ->and($fieldCollection->fields->first())
            ->name->toBe('field1')
            ->type->toBe(FieldTypeEnum::STRING)
            ->required->toBeTrue()
            ->min->toBeNull()
            ->max->toBeNull()
            ->label->toBe('Field1');
    });

    it('creates collection using from() method', function () {
        $fields = [
            ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
            ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
        ];

        $fieldCollection = FieldCollectionData::from(['fields' => $fields]);

        expect($fieldCollection->fields)
            ->toHaveCount(2)
            ->each->toBeInstanceOf(FieldData::class)
            ->and($fieldCollection->fields->first())
            ->name->toBe('field1')
            ->type->toBe(FieldTypeEnum::STRING)
            ->required->toBeTrue()
            ->min->toBeNull()
            ->max->toBeNull()
            ->label->toBe('Field1');
    });

    it('creates collection from Collection instance', function () {
        $fields = collect([
            ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
            ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
        ]);

        $fieldCollection = new FieldCollectionData($fields);

        expect($fieldCollection->fields)
            ->toHaveCount(2)
            ->each->toBeInstanceOf(FieldData::class)
            ->and($fieldCollection->fields->first())
            ->name->toBe('field1')
            ->type->toBe(FieldTypeEnum::STRING)
            ->required->toBeTrue()
            ->min->toBeNull()
            ->max->toBeNull()
            ->label->toBe('Field1');
    });

    it('preserves field order', function () {
        $fields = [
            ['name' => 'first', 'type' => FieldTypeEnum::STRING],
            ['name' => 'second', 'type' => FieldTypeEnum::INTEGER],
            ['name' => 'third', 'type' => FieldTypeEnum::BOOLEAN],
        ];

        $fieldCollection = new FieldCollectionData($fields);

        expect($fieldCollection->fields->pluck('name')->toArray())
            ->toBe(['first', 'second', 'third']);
    });
});

describe('validation rules generation', function () {
    it('generates validation rules for all field types', function () {
        $fields = [
            ['name' => 'username', 'type' => FieldTypeEnum::STRING, 'min' => 3, 'max' => 20],
            ['name' => 'password', 'type' => FieldTypeEnum::PASSWORD, 'required' => true],
            ['name' => 'email', 'type' => FieldTypeEnum::EMAIL, 'required' => true, 'max' => 100],
            ['name' => 'age', 'type' => FieldTypeEnum::INTEGER, 'required' => false, 'min' => 0, 'max' => 120],
            ['name' => 'bio', 'type' => FieldTypeEnum::TEXT, 'required' => false, 'max' => 500],
            ['name' => 'accept_terms', 'type' => FieldTypeEnum::BOOLEAN, 'required' => true],
        ];

        $fieldCollection = new FieldCollectionData($fields);
        $rules = $fieldCollection->asValidationRules();

        expect($rules)
            ->toBeArray()
            ->toHaveCount(6)
            ->toHaveKeys(['username', 'password', 'email', 'age', 'bio', 'accept_terms'])
            ->and($rules['username'])->toMatchArray(['required', 'string', 'min:3', 'max:20'])
            ->and($rules['password'])->toMatchArray(['required', 'string'])
            ->and($rules['email'])->toMatchArray(['required', 'email', 'max:100'])
            ->and($rules['age'])->toMatchArray(['nullable', 'integer', 'min:0', 'max:120'])
            ->and($rules['bio'])->toMatchArray(['nullable', 'string', 'max:500'])
            ->and($rules['accept_terms'])->toMatchArray(['required', 'boolean']);
    });

    it('returns empty array for empty collection', function () {
        $fieldCollection = new FieldCollectionData();

        expect($fieldCollection->asValidationRules())
            ->toBeArray()
            ->toBeEmpty();
    });

    it('generates rules in consistent format', function () {
        $fields = [
            ['name' => 'field1', 'type' => FieldTypeEnum::STRING],
            ['name' => 'field2', 'type' => FieldTypeEnum::INTEGER],
            ['name' => 'field3', 'type' => FieldTypeEnum::BOOLEAN],
            ['name' => 'field3', 'type' => FieldTypeEnum::PASSWORD],
        ];

        $fieldCollection = new FieldCollectionData($fields);
        $rules = $fieldCollection->asValidationRules();

        expect($rules)
            ->each(fn ($rule) => $rule->toBeArray()->not->toBeEmpty());
    });
});

describe('form representation generation', function () {
    it('generates form representation for all field types', function () {
        $fields = [
            ['name' => 'username', 'type' => FieldTypeEnum::STRING, 'min' => 3, 'max' => 20],
            ['name' => 'password', 'type' => FieldTypeEnum::PASSWORD, 'required' => true],
            ['name' => 'email', 'type' => FieldTypeEnum::EMAIL, 'required' => true, 'max' => 100],
            ['name' => 'age', 'type' => FieldTypeEnum::INTEGER, 'required' => false, 'min' => 0, 'max' => 120],
            ['name' => 'bio', 'type' => FieldTypeEnum::TEXT, 'required' => false, 'max' => 500],
            ['name' => 'accept_terms', 'type' => FieldTypeEnum::BOOLEAN, 'required' => true],
        ];

        $fieldCollection = new FieldCollectionData($fields);
        $form = $fieldCollection->asForm();

        expect($form)
            ->toBeArray()
            ->toHaveCount(6)
            ->each->toHaveKeys(['name', 'widget', 'label', 'rules'])
            // String field
            ->and($form[0])
            ->name->toBe('username')
            ->widget->toBe('text')
            ->label->toBe('Username')
            ->rules->toMatchArray(['required' => true, 'minlength' => 3, 'maxlength' => 20])
            // Password field
            ->and($form[1])
            ->name->toBe('password')
            ->widget->toBe('password')
            ->label->toBe('Password')
            ->rules->toMatchArray(['required' => true])
            // Boolean field
            ->and($form[5])
            ->name->toBe('accept_terms')
            ->widget->toBe('checkbox')
            ->label->toBe('Accept Terms')
            ->rules->toMatchArray(['required' => true]);
    });

    it('returns empty array for empty collection', function () {
        $fieldCollection = new FieldCollectionData();

        expect($fieldCollection->asForm())
            ->toBeArray()
            ->toBeEmpty();
    });

    it('generates correct widget for each field type', function (FieldTypeEnum $type, string $expectedWidget) {
        $fieldCollection = new FieldCollectionData([
            ['name' => 'test', 'type' => $type],
        ]);

        $form = $fieldCollection->asForm();

        expect($form)->toHaveKey('0.widget', $expectedWidget);
    })->with([
        'string -> text' => [FieldTypeEnum::STRING, 'text'],
        'text -> textarea' => [FieldTypeEnum::TEXT, 'textarea'],
        'password -> password' => [FieldTypeEnum::PASSWORD, 'password'],
        'email -> email' => [FieldTypeEnum::EMAIL, 'email'],
        'url -> url' => [FieldTypeEnum::URL, 'url'],
        'integer -> number' => [FieldTypeEnum::INTEGER, 'number'],
        'boolean -> checkbox' => [FieldTypeEnum::BOOLEAN, 'checkbox'],
    ]);

    it('generates human-readable labels', function () {
        $fieldCollection = new FieldCollectionData([
            ['name' => 'first_name', 'type' => FieldTypeEnum::STRING],
            ['name' => 'email_address', 'type' => FieldTypeEnum::EMAIL],
        ]);

        $form = $fieldCollection->asForm();

        expect($form)->toHaveCount(2)
            ->toHaveKey('0.label', 'First Name')
            ->toHaveKey('1.label', 'Email Address');
    });

    it('generates empty rules for optional fields without constraints', function () {
        $fieldCollection = new FieldCollectionData([
            ['name' => 'optional', 'type' => FieldTypeEnum::STRING, 'required' => false],
        ]);

        $form = $fieldCollection->asForm();

        expect($form[0]['rules'])->toBeArray()->toBeEmpty();
    });

    it('maintains field order in form representation', function () {
        $fields = [
            ['name' => 'first', 'type' => FieldTypeEnum::STRING],
            ['name' => 'second', 'type' => FieldTypeEnum::INTEGER],
            ['name' => 'third', 'type' => FieldTypeEnum::BOOLEAN],
        ];

        $fieldCollection = new FieldCollectionData($fields);
        $form = $fieldCollection->asForm();

        expect(array_column($form, 'name'))->toBe(['first', 'second', 'third']);
    });
});

it('does not modify original fields array', function () {
    $fields = [
        ['name' => 'test', 'type' => FieldTypeEnum::STRING],
    ];

    $original = $fields;
    $fieldCollection = new FieldCollectionData($fields);

    $fieldCollection->asValidationRules();
    $fieldCollection->asForm();

    expect($fields)->toBe($original);
});
