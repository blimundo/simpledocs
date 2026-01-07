<?php

declare(strict_types=1);

namespace App\Data\Fields;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class FieldCollectionData extends Data
{
    /** @var Collection<int, FieldData> */
    public readonly Collection $fields;

    /**
     * @param  array<int, FieldData>|Collection<int, FieldData>  $fields
     */
    public function __construct(
        array|Collection $fields = [],
    ) {
        $this->fields = collect($fields)->map(fn ($field) => FieldData::from($field));
    }

    /**
     * Get the validation rules for all fields in the collection.
     *
     * @return array<string, array<int, string>>
     */
    public function asValidationRules(): array
    {
        return $this->fields->mapWithKeys(function (FieldData $field) {
            return [$field->name => $field->asValidationRules()];
        })->toArray();
    }

    /**
     * Get the form representation for all fields in the collection.
     *
     * @return array<int, array<string, mixed>>
     */
    public function asForm(): array
    {
        return $this->fields->map(
            fn (FieldData $field) => $field->asFormField()
        )->toArray();
    }
}
