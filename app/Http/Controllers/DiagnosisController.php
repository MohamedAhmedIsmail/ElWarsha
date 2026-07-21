<?php

namespace App\Http\Controllers;

use App\Http\Requests\Diagnosis\DiagnosisSuggestionsRequest;
use App\Http\Requests\Diagnosis\StoreDiagnosisRequest;
use App\Http\Requests\Diagnosis\UploadDiagnosisMediaRequest;
use App\Http\Resources\DiagnosisMediaResource;
use App\Http\Resources\DiagnosisResource;
use App\Http\Resources\DiagnosisWorkshopSuggestionResource;
use App\Models\User;
use App\Services\DiagnosisService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    public function __construct(private readonly DiagnosisService $diagnosisService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Diagnoses retrieved successfully.', [
            'items' => DiagnosisResource::collection($this->diagnosisService->listForUser($user)),
        ]);
    }

    public function store(StoreDiagnosisRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Diagnosis completed successfully.', [
            'diagnosis' => new DiagnosisResource($this->diagnosisService->create($user, $request->toDto())),
        ], 201);
    }

    public function show(int $diagnosis, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Diagnosis retrieved successfully.', [
            'diagnosis' => new DiagnosisResource($this->diagnosisService->getForUser($user, $diagnosis)),
        ]);
    }

    public function uploadMedia(int $diagnosis, UploadDiagnosisMediaRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Diagnosis media uploaded successfully.', [
            'items' => DiagnosisMediaResource::collection($this->diagnosisService->uploadMedia($user, $diagnosis, $request->toDto())),
        ], 201);
    }

    public function recommendedWorkshops(int $diagnosis, DiagnosisSuggestionsRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Recommended workshops retrieved successfully.', [
            'items' => DiagnosisWorkshopSuggestionResource::collection($this->diagnosisService->recommendedWorkshops($user, $diagnosis, $request->toDto())),
        ]);
    }
}
