<?php

namespace App\Http\Requests;

use App\Enums\AccessCategory;
use App\Enums\ImportanceLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'importance' => ['sometimes', 'nullable', Rule::enum(ImportanceLevel::class)],
            'category' => ['sometimes', 'nullable', Rule::enum(AccessCategory::class)],
            'is_active' => ['sometimes', 'nullable', 'boolean'],
            'sort' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in([
                    'title',
                    'created_on',
                    'reading_time_minutes',
                ]),
            ],
            'direction' => ['sometimes', 'nullable', 'string', Rule::in(['asc', 'desc'])],
        ];
    }
}
