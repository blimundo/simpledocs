<?php

declare(strict_types=1);

use App\Data\Fields\FieldData;
use App\Enums\FieldTypeEnum;

uses()->group('unit', 'data', 'fields');

it('can be instantiated with all properties', function () {
    $fieldData = new FieldData(
        name: 'test_field',
        type: FieldTypeEnum::STRING,
        label: 'Test Field',
        required: true,
        min: 0,
        max: 100,
    );

    expect($fieldData)
        ->name->toBe('test_field')
        ->type->toBe(FieldTypeEnum::STRING)
        ->label->toBe('Test Field')
        ->required->toBeTrue()
        ->min->toBe(0)
        ->max->toBe(100);
});

describe('optional properties', function () {
    it('applies default values correctly', function () {
        $fieldData = new FieldData(
            name: 'test_field',
            type: FieldTypeEnum::STRING
        );

        expect($fieldData)
            ->label->toBe('Test Field')
            ->required->toBeTrue()
            ->min->toBeNull()
            ->max->toBeNull();
    });

    it('generates label from name correctly', function (string $name, string $expectedLabel) {
        $fieldData = new FieldData(name: $name, type: FieldTypeEnum::STRING);

        expect($fieldData->label)->toBe($expectedLabel);
    })->with([
        'snake_case' => ['snake_case_field', 'Snake Case Field'],
        'camelCase' => ['camelCaseField', 'Camel Case Field'],
        'single word' => ['field', 'Field'],
        'with numbers' => ['field_123_test', 'Field 123 Test'],
    ]);
});

describe('validation', function () {
    it('throws error if name is empty', function () {
        new FieldData(name: '', type: FieldTypeEnum::STRING);
    })->throws(InvalidArgumentException::class, 'name cannot be empty.');

    it('handles various whitespace scenarios', function (string $input, string $expected) {
        $fieldData = new FieldData(name: $input, type: FieldTypeEnum::STRING);

        expect($fieldData->name)->toBe($expected);
    })->with([
        'leading spaces' => ['  name', 'name'],
        'trailing spaces' => ['name  ', 'name'],
        'both' => ['  name  ', 'name'],
        'tabs' => ["\tname\t", 'name'],
        'mixed' => [" \t name \t ", 'name'],
    ]);

    it('accepts valid min and max values', function () {
        $fieldData = new FieldData(
            name: 'field',
            type: FieldTypeEnum::INTEGER,
            min: 0,
            max: 100
        );

        expect($fieldData->min)->toBe(0)
            ->and($fieldData->max)->toBe(100);
    });

    it('handles min equal to max', function () {
        $fieldData = new FieldData(
            name: 'field',
            type: FieldTypeEnum::INTEGER,
            min: 10,
            max: 10
        );

        expect($fieldData->min)->toBe(10)
            ->and($fieldData->max)->toBe(10);
    });
});

describe('rules generation', function () {
    it('generates rules correctly for string types', function (FieldTypeEnum $type, string $laravelType, array $data, array $expectedRules) {
        $fieldData = FieldData::from(array_merge($data, ['type' => $type]));

        expect($fieldData->asValidationRules())
            ->toBeArray()
            ->toEqualCanonicalizing(array_merge([$laravelType], $expectedRules));
    })->with('rule_string_types', 'rules.string');

    it('generates rules correctly for boolean types', function (FieldData $data, array $expectedRules) {
        expect($data->asValidationRules())
            ->toBeArray()
            ->toMatchArray($expectedRules);
    })->with('rules.boolean');

    it('generates rules correctly for numeric types', function (FieldTypeEnum $type, string $laravelType, array $data, array $expectedRules) {
        $fieldData = FieldData::from(array_merge($data, ['type' => $type]));

        expect($fieldData->asValidationRules())
            ->toBeArray()
            ->toEqualCanonicalizing(array_merge($expectedRules, [$laravelType]));
    })->with('rule_numeric_types', 'rules.numeric');
});

describe('form generation', function () {
    it('generates form representation for string types', function (FieldTypeEnum $type, string $widget, array $data, array $expectedForm) {
        $fieldData = FieldData::from(array_merge($data, ['type' => $type]));

        expect($fieldData->asFormField())
            ->toBeArray()
            ->toMatchArray(array_merge($expectedForm, ['widget' => $widget]));
    })->with('form_string_types', 'form.string');

    it('generates form representation for numeric types', function (FieldTypeEnum $type, string $widget, array $data, array $expectedForm) {
        $fieldData = FieldData::from(array_merge($data, ['type' => $type]));

        expect($fieldData->asFormField())
            ->toBeArray()
            ->toMatchArray(array_merge($expectedForm, ['widget' => $widget]));
    })->with('form_numeric_types', 'form.numeric');

    it('generates form representation for boolean types', function (FieldData $data, array $expectedForm) {
        expect($data->asFormField())
            ->toBeArray()
            ->toMatchArray($expectedForm);
    })->with('form.boolean');

    // MELHORIA 12: Testar mapeamento widget correto
    it('maps field types to correct widgets', function (FieldTypeEnum $type, string $expectedWidget) {
        $fieldData = new FieldData(name: 'test', type: $type);

        expect($fieldData->asFormField()['widget'])->toBe($expectedWidget);
    })->with([
        'string -> text' => [FieldTypeEnum::STRING, 'text'],
        'text -> textarea' => [FieldTypeEnum::TEXT, 'textarea'],
        'password -> password' => [FieldTypeEnum::PASSWORD, 'password'],
        'email -> email' => [FieldTypeEnum::EMAIL, 'email'],
        'url -> url' => [FieldTypeEnum::URL, 'url'],
        'integer -> number' => [FieldTypeEnum::INTEGER, 'number'],
        'boolean -> checkbox' => [FieldTypeEnum::BOOLEAN, 'checkbox'],
    ]);
});

/* --- datasets --- */

// field type datasets
dataset('rule_string_types', [
    'string' => [FieldTypeEnum::STRING, 'string'],
    'text' => [FieldTypeEnum::TEXT, 'string'],
    'password' => [FieldTypeEnum::PASSWORD, 'string'],
    'key' => [FieldTypeEnum::KEY, 'string'],
    'email' => [FieldTypeEnum::EMAIL, 'email'],
    'url' => [FieldTypeEnum::URL, 'url'],
]);

dataset('rule_numeric_types', [
    'integer' => [FieldTypeEnum::INTEGER, 'integer'],
]);

dataset('form_string_types', [
    'string' => [FieldTypeEnum::STRING, 'text'],
    'text' => [FieldTypeEnum::TEXT, 'textarea'],
    'password' => [FieldTypeEnum::PASSWORD, 'password'],
    'key' => [FieldTypeEnum::KEY, 'text'],
    'email' => [FieldTypeEnum::EMAIL, 'email'],
    'url' => [FieldTypeEnum::URL, 'url'],
]);

dataset('form_numeric_types', [
    'integer' => [FieldTypeEnum::INTEGER, 'number'],
]);

// validation rules datasets
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

// form representation datasets
dataset('form.string', [
    'basic' => [
        ['name' => 'username'],
        [
            'name' => 'username',
            'label' => 'Username',
            'rules' => [
                'required' => true,
            ],
        ],
    ],
    'with_label_and_optional' => [
        ['name' => 'email', 'label' => 'Email Address', 'required' => false],
        [
            'name' => 'email',
            'label' => 'Email Address',
            'rules' => [],
        ],
    ],
    'with_min' => [
        ['name' => 'bio', 'min' => 10],
        [
            'name' => 'bio',
            'label' => 'Bio',
            'rules' => [
                'required' => true,
                'minlength' => 10,
            ],
        ],
    ],
    'with_max' => [
        ['name' => 'title', 'max' => 50, 'required' => false],
        [
            'name' => 'title',
            'label' => 'Title',
            'rules' => [
                'maxlength' => 50,
            ],
        ],
    ],
    'with_min_max' => [
        ['name' => 'password', 'min' => 8, 'max' => 20],
        [
            'name' => 'password',
            'label' => 'Password',
            'rules' => [
                'required' => true,
                'minlength' => 8,
                'maxlength' => 20,
            ],
        ],
    ],
]);

dataset('form.numeric', [
    'basic' => [
        ['name' => 'age'],
        [
            'name' => 'age',
            'label' => 'Age',
            'rules' => [
                'required' => true,
            ],
        ],
    ],
    'with_label_and_optional' => [
        ['name' => 'score', 'label' => 'Score', 'required' => false],
        [
            'name' => 'score',
            'label' => 'Score',
            'rules' => [],
        ],
    ],
    'with_min' => [
        ['name' => 'quantity', 'min' => 1],
        [
            'name' => 'quantity',
            'label' => 'Quantity',
            'rules' => [
                'required' => true,
                'min' => 1,
            ],
        ],
    ],
    'with_max' => [
        ['name' => 'level', 'max' => 10, 'required' => false],
        [
            'name' => 'level',
            'label' => 'Level',
            'rules' => [
                'max' => 10,
            ],
        ],
    ],
    'with_min_max' => [
        ['name' => 'rank', 'min' => 1, 'max' => 100],
        [
            'name' => 'rank',
            'label' => 'Rank',
            'rules' => [
                'required' => true,
                'min' => 1,
                'max' => 100,
            ],
        ],
    ],
]);

dataset('form.boolean', [
    'required' => [
        new FieldData(name: 'is_active', type: FieldTypeEnum::BOOLEAN),
        [
            'name' => 'is_active',
            'label' => 'Is Active',
            'widget' => 'checkbox',
            'rules' => [
                'required' => true,
            ],
        ],
    ],
    'optional' => [
        new FieldData(name: 'is_subscribed', type: FieldTypeEnum::BOOLEAN, required: false),
        [
            'name' => 'is_subscribed',
            'label' => 'Is Subscribed',
            'widget' => 'checkbox',
            'rules' => [],
        ],
    ],
    'ignore_min_max' => [
        new FieldData(name: 'has_access', type: FieldTypeEnum::BOOLEAN, min: 5, max: 10),
        [
            'name' => 'has_access',
            'label' => 'Has Access',
            'widget' => 'checkbox',
            'rules' => [
                'required' => true,
            ],
        ],
    ],
]);
