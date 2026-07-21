<?php

namespace App\Http\Requests\Crm;

use App\DTOs\Crm\StoreLeadNoteData;
use Illuminate\Foundation\Http\FormRequest;

class StoreLeadNoteRequest extends FormRequest
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
        return [
            'note' => ['required', 'string', 'max:2000'],
        ];
    }

    public function toDto(): StoreLeadNoteData
    {
        return new StoreLeadNoteData($this->validated('note'));
    }
}
