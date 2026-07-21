<?php

namespace App\DTOs\ServiceLedger;

use App\Enums\ServiceLedgerMediaType;

class UploadServiceLedgerMediaData
{
    /**
     * @param array<int, \Illuminate\Http\UploadedFile> $files
     */
    public function __construct(
        public readonly ServiceLedgerMediaType $mediaType,
        public readonly array $files,
    ) {
    }
}
