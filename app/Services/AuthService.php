<?php

namespace App\Services;

use App\DTOs\Auth\LoginUserData;
use App\DTOs\Auth\OtpRequestData;
use App\DTOs\Auth\OtpVerificationData;
use App\DTOs\Auth\RegisterUserData;
use App\DTOs\Auth\UpdateProfileData;
use App\Enums\UserStatus;
use App\Models\OtpCode;
use App\Models\User;
use App\Repositories\Contracts\OtpCodeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    private const OTP_TTL_MINUTES = 5;

    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly OtpCodeRepositoryInterface $otpCodes,
    ) {
    }

    public function requestOtp(OtpRequestData $data): OtpCode
    {
        $code = (string) random_int(100000, 999999);

        return $this->otpCodes->create($data->phone, $code, $data->purpose, self::OTP_TTL_MINUTES);
    }

    public function verifyOtp(OtpVerificationData $data): OtpCode
    {
        $otpCode = $this->otpCodes->findLatestUsable($data->phone, $data->code, $data->purpose);

        if (! $otpCode) {
            throw ValidationException::withMessages([
                'code' => __('The OTP code is invalid or expired.'),
            ]);
        }

        return DB::transaction(fn (): OtpCode => $this->otpCodes->markAsUsed($otpCode));
    }

    /**
     * @return array{user: User, token: string}
     */
    public function register(RegisterUserData $data): array
    {
        $user = DB::transaction(fn (): User => $this->users->create($data));

        return [
            'user' => $user,
            'token' => $this->createToken($user),
        ];
    }

    /**
     * @return array{user: User, token: string}
     */
    public function login(LoginUserData $data): array
    {
        $user = $this->users->findByPhone($data->phone);

        if (! $user || ! Hash::check($data->password, (string) $user->password)) {
            throw ValidationException::withMessages([
                'phone' => __('The provided credentials are incorrect.'),
            ]);
        }

        if ($user->status === UserStatus::Blocked) {
            throw new AuthenticationException('This account is blocked.');
        }

        $user->forceFill(['last_login_at' => now()])->save();

        return [
            'user' => $user->refresh(),
            'token' => $this->createToken($user),
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function updateProfile(User $user, UpdateProfileData $data): User
    {
        return $this->users->updateProfile($user, $data);
    }

    private function createToken(User $user): string
    {
        return $user->createToken('mobile')->plainTextToken;
    }
}
