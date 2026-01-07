<?php

declare(strict_types=1);

namespace App\Enums;

enum FieldTypeEnum: string
{
    case BOOLEAN = 'boolean';
    case EMAIL = 'email';
    case INTEGER = 'integer';
    case KEY = 'key';
    case PASSWORD = 'password';
    case STRING = 'string';
    case TEXT = 'text';
    case URL = 'url';

    /**
     * Get the corresponding Laravel validation type.
     */
    public function getLaravelType(): string
    {
        return match ($this) {
            self::BOOLEAN => 'boolean',
            self::EMAIL => 'email',
            self::INTEGER => 'integer',
            self::URL => 'url',
            default => 'string',
        };
    }

    /**
     * Determine if the field type supports length constraints.
     */
    public function hasLengthConstraints(): bool
    {
        return match ($this) {
            self::BOOLEAN => false,

            default => true,
        };
    }

    /**
     * Get the appropriate form widget for this field type.
     */
    public function getFormWidget(): string
    {
        return match ($this) {
            self::BOOLEAN => 'checkbox',
            self::EMAIL => 'email',
            self::INTEGER => 'number',
            self::PASSWORD => 'password',
            self::TEXT => 'textarea',
            self::URL => 'url',
            default => 'text',
        };
    }
}
