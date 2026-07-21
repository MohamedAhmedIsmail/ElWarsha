<?php

namespace App\Http\Controllers;

use App\Http\Requests\Crm\LeadIndexRequest;
use App\Http\Requests\Crm\StoreLeadNoteRequest;
use App\Http\Requests\Crm\UpdateLeadStatusRequest;
use App\Http\Resources\LeadNoteResource;
use App\Http\Resources\LeadResource;
use App\Models\User;
use App\Services\WorkshopCrmService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkshopCrmController extends Controller
{
    public function __construct(private readonly WorkshopCrmService $workshopCrmService)
    {
    }

    public function index(LeadIndexRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Leads retrieved successfully.', [
            'items' => LeadResource::collection($this->workshopCrmService->listLeads($user, $request->toDto())),
        ]);
    }

    public function show(int $lead, Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Lead retrieved successfully.', [
            'lead' => new LeadResource($this->workshopCrmService->getLead($user, $lead)),
        ]);
    }

    public function updateStatus(int $lead, UpdateLeadStatusRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Lead status updated successfully.', [
            'lead' => new LeadResource($this->workshopCrmService->updateLeadStatus($user, $lead, $request->toDto())),
        ]);
    }

    public function addNote(int $lead, StoreLeadNoteRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('Lead note created successfully.', [
            'note' => new LeadNoteResource($this->workshopCrmService->addLeadNote($user, $lead, $request->toDto())),
        ], 201);
    }

    public function analytics(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return ApiResponse::success('CRM analytics retrieved successfully.', [
            'analytics' => $this->workshopCrmService->analytics($user),
        ]);
    }
}
