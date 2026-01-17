<?php

declare(strict_types=1);

namespace App\Http\Requests\Disks\Disks;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateDiskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->disk) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:100'],
            'config' => ['sometimes', 'required', 'array'],
            'size' => ['sometimes', 'required', 'numeric', 'min:1'],
            'used' => ['sometimes', 'nullable', 'numeric', 'min:0', 'lte:size'],
        ];
    }
}
