<?php

declare(strict_types=1);

namespace App\Data\Fields;

use App\Enums\FieldTypeEnum;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

final class FieldData extends Data
{
    public readonly string $name;

    public readonly string $label;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $name,
        public readonly FieldTypeEnum $type,
        ?string $label = null,
        public readonly bool $required = true,
        public readonly ?int $min = null,
        public readonly ?int $max = null,
    ) {
        $name = mb_trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException('name cannot be empty.');
        }

        $this->name = $name;
        $this->label = $label ?? $this->generateLabel($name);
    }

    /**
     * Get the Laravel validation rules for this field.
     *
     * @return array<int, string>
     */
    public function asValidationRules(): array
    {
        $rules[] = $this->required ? 'required' : 'nullable';

        $rules[] = $this->type->getLaravelType();

        if ($this->type->hasLengthConstraints()) {
            if (! is_null($this->min)) {
                $rules[] = 'min:'.$this->min;
            }

            if (! is_null($this->max)) {
                $rules[] = 'max:'.$this->max;
            }
        }

        return $rules;
    }

    /**
     * Get the form field representation for this field.
     *
     * @return array<string, mixed>
     */
    public function asFormField(): array
    {
        $widget = $this->type->getFormWidget();
        $rules = [];

        if ($this->required) {
            $rules['required'] = true;
        }

        if ($this->type->hasLengthConstraints()) {
            if (! is_null($this->min)) {
                $rules[$widget === 'number' ? 'min' : 'minlength'] = $this->min;
            }

            if (! is_null($this->max)) {
                $rules[$widget === 'number' ? 'max' : 'maxlength'] = $this->max;
            }
        }

        return [
            'name' => $this->name,
            'label' => $this->label,
            'widget' => $widget,
            'rules' => $rules,
        ];
    }

    /**
     * Generate a human-readable label from the field name.
     */
    private function generateLabel(string $name): string
    {
        $name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $name);

        return ucwords(str_replace('_', ' ', $name));
    }
}
