<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Diagnosis\DummyDiagnosisResult;
use App\DTOs\Diagnosis\StoreDiagnosisData;
use App\Enums\DiagnosisStatus;
use App\Models\Diagnosis;
use App\Repositories\Contracts\DiagnosisRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DiagnosisRepository implements DiagnosisRepositoryInterface
{
    public function listForUser(int $userId): Collection
    {
        return Diagnosis::query()
            ->ownedBy($userId)
            ->with(['vehicle.brand', 'vehicle.model', 'affectedCategory', 'media'])
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $diagnosisId): ?Diagnosis
    {
        return Diagnosis::query()
            ->ownedBy($userId)
            ->with(['vehicle.brand', 'vehicle.model', 'affectedCategory', 'media'])
            ->whereKey($diagnosisId)
            ->first();
    }

    public function createPending(int $userId, StoreDiagnosisData $data): Diagnosis
    {
        return Diagnosis::query()->create([
            'user_id' => $userId,
            'vehicle_id' => $data->vehicleId,
            'description' => $data->description,
            'symptoms_json' => $data->symptomsJson,
            'status' => DiagnosisStatus::Pending,
            'disclaimer_accepted' => $data->disclaimerAccepted,
        ]);
    }

    public function complete(Diagnosis $diagnosis, DummyDiagnosisResult $result): Diagnosis
    {
        $diagnosis->forceFill([
            'ai_response' => $result->aiResponse,
            'diagnosis_text' => $result->diagnosisText,
            'confidence' => $result->confidence,
            'urgency' => $result->urgency,
            'affected_category_id' => $result->affectedCategory?->id,
            'recommend_professional' => $result->recommendProfessional,
            'status' => DiagnosisStatus::Completed,
        ])->save();

        return $diagnosis->refresh()->load(['vehicle.brand', 'vehicle.model', 'affectedCategory', 'media']);
    }
}
