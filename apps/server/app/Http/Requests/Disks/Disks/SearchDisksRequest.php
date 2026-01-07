<?php

declare(strict_types=1);

namespace App\Http\Requests\Disks\Disks;

use App\Enums\DiskTypeEnum;
use App\Models\Disk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SearchDisksRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Disk::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::enum(DiskTypeEnum::class)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:50'],
            'sortOrder' => ['nullable', Rule::in(['asc', 'desc'])],
        ];
    }
}
