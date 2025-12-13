<?php

namespace App\Data\Fields;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class FieldCollectionData extends Data
{
    public readonly Collection $fields;

    public function __construct(
        array|Collection $fields = [],
    ) {
        $this->fields = collect($fields)->map(fn($field) => FieldData::from($field));
    }

    /**
     * Get the validation rules for all fields in the collection.
     */
    public function asValidationRules(): array
    {
        return $this->fields->mapWithKeys(function (FieldData $field) {
            return [$field->name => $field->asValidationRules()];
        })->toArray();
    }
}
