<?php

namespace App\Repositories\Contracts;

use App\Enums\OtpPurpose;
use App\Models\OtpCode;

interface OtpCodeRepositoryInterface
{
    public function create(string $phone, string $code, OtpPurpose $purpose, int $ttlMinutes): OtpCode;

    public function findLatestUsable(string $phone, string $code, OtpPurpose $purpose): ?OtpCode;

    public function markAsUsed(OtpCode $otpCode): OtpCode;
}
