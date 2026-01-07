<?php

declare(strict_types=1);

namespace App\Http\Requests\Disks\Disks;

use App\Models\Disk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreDiskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Disk::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'type' => ['required', Rule::enum(\App\Enums\DiskTypeEnum::class)],
            'config' => ['required', 'array'],
            'size' => ['required', 'numeric', 'min:1'],
            'used' => ['nullable', 'numeric', 'min:0', 'lte:size'],
        ];
    }
}
