<?php

namespace App\DTOs\Sos;

use App\Enums\SosUrgency;
use Illuminate\Http\UploadedFile;

final readonly class StoreSosRequestData
{
    public function __construct(
        public int $sosServiceTypeId,
        public float $latitude,
        public float $longitude,
        public ?int $vehicleId,
        public ?string $description,
        public ?UploadedFile $image,
        public SosUrgency $urgency,
    ) {
    }
}
