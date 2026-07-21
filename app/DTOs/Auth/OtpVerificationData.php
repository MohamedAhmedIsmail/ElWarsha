<?php

namespace App\DTOs\Auth;

use App\Enums\OtpPurpose;

final readonly class OtpVerificationData
{
    public function __construct(
        public string $phone,
        public string $code,
        public OtpPurpose $purpose,
    ) {
    }
}
