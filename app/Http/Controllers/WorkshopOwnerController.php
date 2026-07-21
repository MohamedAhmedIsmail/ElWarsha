<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\Workshop\RegisterWorkshopRequest;
use App\Http\Requests\Workshop\SyncWorkshopBrandsRequest;
use App\Http\Requests\Workshop\SyncWorkshopServicesRequest;
use App\Http\Requests\Workshop\SyncWorkshopWorkingHoursRequest;
use App\Http\Requests\Workshop\UpdateWorkshopProfileRequest;
use App\Http\Requests\Workshop\UploadWorkshopImagesRequest;
use App\Http\Resources\WorkshopImageResource;
use App\Http\Resources\WorkshopResource;
use App\Models\User;
use App\Services\WorkshopOwnerService;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopOwnerController extends Controller
{
    public function __construct(private readonly WorkshopOwnerService $workshopOwnerService)
    {
    }

    public function register(RegisterWorkshopRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop submitted for approval successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->register($user, $request->toDto())),
        ], 201);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $this->workshopOwner($request);

        return ApiResponse::success('Workshop profile retrieved successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->profile($user)),
        ]);
    }

    public function updateProfile(UpdateWorkshopProfileRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop profile updated successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->updateProfile($user, $request->toDto())),
        ]);
    }

    public function uploadImages(UploadWorkshopImagesRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop images uploaded successfully.', [
            'items' => WorkshopImageResource::collection($this->workshopOwnerService->uploadImages($user, $request->toDto())),
        ], 201);
    }

    public function deleteImage(int $image, Request $request): JsonResponse
    {
        $this->workshopOwnerService->deleteImage($this->workshopOwner($request), $image);

        return ApiResponse::success('Workshop image deleted successfully.');
    }

    public function syncServices(SyncWorkshopServicesRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop services updated successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->syncServices($user, $request->toDto())),
        ]);
    }

    public function syncBrands(SyncWorkshopBrandsRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop brands updated successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->syncBrands($user, $request->toDto())),
        ]);
    }

    public function syncWorkingHours(SyncWorkshopWorkingHoursRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Workshop working hours updated successfully.', [
            'workshop' => new WorkshopResource($this->workshopOwnerService->syncWorkingHours($user, $request->toDto())),
        ]);
    }

    private function workshopOwner(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();
        $role = $user->role;
        $isWorkshopOwner = $role instanceof UserRole
            ? $role === UserRole::WorkshopOwner
            : $role === UserRole::WorkshopOwner->value;

        if (! $isWorkshopOwner) {
            throw new AuthorizationException('Only workshop owners can perform this action.');
        }

        return $user;
    }
}
