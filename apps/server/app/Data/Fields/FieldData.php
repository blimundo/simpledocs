<?php

namespace App\Data\Fields;

use App\Enums\FieldTypeEnum;
use Spatie\LaravelData\Data;

class FieldData extends Data
{
    public readonly string $name;
    public readonly string $label;

    /**
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $name,
        public readonly FieldTypeEnum $type,
        ?string $label = null,
        public readonly bool $required = true,
        public readonly ?int $min = null,
        public readonly ?int $max = null,
    ) {
        $name = trim($name);

        if (empty($name)) {
            throw new \InvalidArgumentException('Field name cannot be empty.');
        }

        $this->name = $name;
        $this->label = $label ?? $this->generateLabel($name);
    }

    /**
     * Generate a human-readable label from the field name.
     */
    private function generateLabel(string $name): string
    {
        return ucwords(str_replace('_', ' ', $name));
    }

    /**
     * Get the Laravel validation rules for this field.
     */
    public function asValidationRules(): array
    {
        $rules[] = $this->required ? 'required' : 'nullable';

        $rules[] = $this->type->getLaravelType();

        if ($this->type->hasLengthConstraints()) {
            if (!is_null($this->min)) {
                $rules[] = 'min:' . $this->min;
            }

            if (!is_null($this->max)) {
                $rules[] = 'max:' . $this->max;
            }
        }

        return $rules;
    }
}
