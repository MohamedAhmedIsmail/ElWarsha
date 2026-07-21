<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopSyncData;

class SyncWorkshopServicesRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_ids' => ['required', 'array'],
            'service_ids.*' => ['integer', 'distinct', 'exists:services,id'],
        ];
    }

    public function toDto(): WorkshopSyncData
    {
        return new WorkshopSyncData(array_map('intval', $this->validated('service_ids')));
    }
}
