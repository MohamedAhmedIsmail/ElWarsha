<?php

namespace App\Repositories\Eloquent;

use App\DTOs\EmergencyGuidance\StoreEmergencyGuidanceData;
use App\Enums\SosUrgency;
use App\Models\EmergencyGuidanceRequest;
use App\Models\SosServiceType;
use App\Repositories\Contracts\EmergencyGuidanceRepositoryInterface;

class EmergencyGuidanceRepository implements EmergencyGuidanceRepositoryInterface
{
    /**
     * @param array<string, mixed> $aiResponse
     */
    public function createForUser(
        int $userId,
        StoreEmergencyGuidanceData $data,
        array $aiResponse,
        SosUrgency $urgency,
        bool $needsSos,
        ?SosServiceType $recommendedSosServiceType,
        string $safetyMessage
    ): EmergencyGuidanceRequest {
        $guidance = EmergencyGuidanceRequest::query()->create([
            'user_id' => $userId,
            'vehicle_id' => $data->vehicleId,
            'description' => $data->description,
            'symptoms_json' => $data->symptoms,
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'ai_response' => $aiResponse,
            'urgency' => $urgency,
            'needs_sos' => $needsSos,
            'recommended_sos_service_type_id' => $recommendedSosServiceType?->id,
            'safety_message' => $safetyMessage,
        ]);

        return $guidance->load(['vehicle.brand', 'vehicle.model', 'recommendedSosServiceType']);
    }
}
