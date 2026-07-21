<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Requests\Auth\OtpVerificationRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService)
    {
    }

    public function requestOtp(OtpRequest $request): JsonResponse
    {
        $otpCode = $this->authService->requestOtp($request->toDto());

        return ApiResponse::success('OTP code generated successfully.', [
            'expires_at' => $otpCode->expires_at->toISOString(),
            'otp' => app()->isLocal() ? $otpCode->code : null,
        ], 201);
    }

    public function verifyOtp(OtpVerificationRequest $request): JsonResponse
    {
        $this->authService->verifyOtp($request->toDto());

        return ApiResponse::success('OTP code verified successfully.');
    }

    public function resendOtp(OtpRequest $request): JsonResponse
    {
        $otpCode = $this->authService->requestOtp($request->toDto());

        return ApiResponse::success('OTP code resent successfully.', [
            'expires_at' => $otpCode->expires_at->toISOString(),
            'otp' => app()->isLocal() ? $otpCode->code : null,
        ], 201);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->toDto());

        return ApiResponse::success('Registered successfully.', [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->toDto());

        return ApiResponse::success('Logged in successfully.', [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $this->authService->logout($user);

        return ApiResponse::success('Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::success('Profile retrieved successfully.', [
            'user' => new UserResource($request->user()),
        ]);
    }

    public function updateMe(UpdateProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $updatedUser = $this->authService->updateProfile($user, $request->toDto());

        return ApiResponse::success('Profile updated successfully.', [
            'user' => new UserResource($updatedUser),
        ]);
    }
}
