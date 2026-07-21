<?php

namespace App\DTOs\Diagnosis;

use App\Enums\DiagnosisMediaType;
use Illuminate\Http\UploadedFile;

final readonly class UploadDiagnosisMediaData
{
    /**
     * @param array<int, UploadedFile> $files
     */
    public function __construct(
        public array $files,
        public DiagnosisMediaType $mediaType,
    ) {
    }
}
