<?php

namespace App\DTOs\Workshop;

use App\Enums\WorkshopImageType;
use Illuminate\Http\UploadedFile;

final readonly class WorkshopImageUploadData
{
    /**
     * @param array<int, UploadedFile> $images
     */
    public function __construct(
        public array $images,
        public WorkshopImageType $type,
        public int $sortOrder,
    ) {
    }
}
