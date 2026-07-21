<?php

namespace App\Repositories\Eloquent;

use App\Enums\OtpPurpose;
use App\Models\OtpCode;
use App\Repositories\Contracts\OtpCodeRepositoryInterface;

class OtpCodeRepository implements OtpCodeRepositoryInterface
{
    public function create(string $phone, string $code, OtpPurpose $purpose, int $ttlMinutes): OtpCode
    {
        return OtpCode::query()->create([
            'phone' => $phone,
            'code' => $code,
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes($ttlMinutes),
        ]);
    }

    public function findLatestUsable(string $phone, string $code, OtpPurpose $purpose): ?OtpCode
    {
        return OtpCode::query()
            ->where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', $purpose->value)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    public function markAsUsed(OtpCode $otpCode): OtpCode
    {
        $otpCode->forceFill(['used_at' => now()])->save();

        return $otpCode->refresh();
    }
}
