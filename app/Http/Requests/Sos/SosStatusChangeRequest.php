<?php

namespace App\Http\Requests\Sos;

use App\DTOs\Sos\SosStatusChangeData;
use Illuminate\Foundation\Http\FormRequest;

class SosStatusChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ['notes' => ['sometimes', 'nullable', 'string']];
    }

    public function toDto(): SosStatusChangeData
    {
        return new SosStatusChangeData($this->validated('notes'));
    }
}
