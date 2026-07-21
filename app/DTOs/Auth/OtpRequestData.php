<?php

namespace App\DTOs\Auth;

use App\Enums\OtpPurpose;

final readonly class OtpRequestData
{
    public function __construct(
        public string $phone,
        public OtpPurpose $purpose,
    ) {
    }
}
