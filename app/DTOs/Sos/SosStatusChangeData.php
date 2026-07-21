<?php

namespace App\DTOs\Sos;

final readonly class SosStatusChangeData
{
    public function __construct(public ?string $notes)
    {
    }
}
