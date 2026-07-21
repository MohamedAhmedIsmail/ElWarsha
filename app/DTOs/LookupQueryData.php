<?php

namespace App\DTOs;

final readonly class LookupQueryData
{
    public function __construct(
        public ?string $search,
        public bool $paginate,
        public int $perPage,
    ) {
    }
}
