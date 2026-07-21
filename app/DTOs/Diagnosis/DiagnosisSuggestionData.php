<?php

namespace App\DTOs\Diagnosis;

final readonly class DiagnosisSuggestionData
{
    public function __construct(
        public ?float $lat,
        public ?float $lng,
        public int $limit,
    ) {
    }
}
