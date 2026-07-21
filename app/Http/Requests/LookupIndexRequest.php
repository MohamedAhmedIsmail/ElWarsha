<?php

namespace App\Http\Requests;

use App\DTOs\LookupQueryData;
use Illuminate\Foundation\Http\FormRequest;

class LookupIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
            'paginate' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toDto(): LookupQueryData
    {
        return new LookupQueryData(
            search: $this->validated('search'),
            paginate: $this->boolean('paginate') || $this->has('per_page'),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
