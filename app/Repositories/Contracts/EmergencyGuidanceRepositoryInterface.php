<?php

namespace App\Repositories\Contracts;

use App\DTOs\EmergencyGuidance\StoreEmergencyGuidanceData;
use App\Enums\SosUrgency;
use App\Models\EmergencyGuidanceRequest;
use App\Models\SosServiceType;

interface EmergencyGuidanceRepositoryInterface
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
    ): EmergencyGuidanceRequest;
}
