<?php

namespace App\Http\Requests\Workshop;

use App\DTOs\Workshop\WorkshopImageUploadData;
use App\Enums\WorkshopImageType;
use Illuminate\Validation\Rules\Enum;

class UploadWorkshopImagesRequest extends WorkshopOwnerRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1', 'max:10'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'type' => ['sometimes', new Enum(WorkshopImageType::class)],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function toDto(): WorkshopImageUploadData
    {
        return new WorkshopImageUploadData(
            images: $this->file('images', []),
            type: WorkshopImageType::from($this->validated('type') ?? WorkshopImageType::Workshop->value),
            sortOrder: (int) ($this->validated('sort_order') ?? 0),
        );
    }
}
