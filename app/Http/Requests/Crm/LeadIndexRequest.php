<?php

namespace App\Http\Requests\Crm;

use App\DTOs\Crm\LeadFilterData;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class LeadIndexRequest extends FormRequest
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
            'source' => ['sometimes', 'nullable', new Enum(LeadSource::class)],
            'status' => ['sometimes', 'nullable', new Enum(LeadStatus::class)],
        ];
    }

    public function toDto(): LeadFilterData
    {
        return new LeadFilterData(
            source: $this->validated('source') === null ? null : LeadSource::from($this->validated('source')),
            status: $this->validated('status') === null ? null : LeadStatus::from($this->validated('status')),
        );
    }
}
