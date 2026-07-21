<?php

namespace App\Http\Requests\Crm;

use App\DTOs\Crm\UpdateLeadStatusData;
use App\Enums\LeadStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLeadStatusRequest extends FormRequest
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
            'status' => ['required', new Enum(LeadStatus::class)],
        ];
    }

    public function toDto(): UpdateLeadStatusData
    {
        return new UpdateLeadStatusData(LeadStatus::from($this->validated('status')));
    }
}
