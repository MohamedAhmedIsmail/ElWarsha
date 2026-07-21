<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopSyncData;

class SyncWorkshopBrandsRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand_ids' => ['required', 'array'],
            'brand_ids.*' => ['integer', 'distinct', 'exists:car_brands,id'],
        ];
    }

    public function toDto(): WorkshopSyncData
    {
        return new WorkshopSyncData(array_map('intval', $this->validated('brand_ids')));
    }
}
