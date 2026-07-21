<?php

namespace App\Services;

use App\DTOs\EmergencyGuidance\StoreEmergencyGuidanceData;
use App\Enums\SosUrgency;
use App\Models\EmergencyGuidanceRequest;
use App\Models\SosServiceType;
use App\Models\User;
use App\Repositories\Contracts\EmergencyGuidanceRepositoryInterface;
use App\Repositories\Contracts\SosServiceTypeRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmergencyGuidanceService
{
    private const HIGH_RISK_KEYWORDS = [
        'smoke',
        'fire',
        'brakes',
        'brake',
        'steering',
        'overheating',
        'accident',
    ];

    private const SAFETY_MESSAGE = 'This emergency guidance is safety-first and is not a repair instruction. Move away from danger and request professional help when risk is present.';

    public function __construct(
        private readonly EmergencyGuidanceRepositoryInterface $guidanceRequests,
        private readonly SosServiceTypeRepositoryInterface $sosServiceTypes,
        private readonly VehicleRepositoryInterface $vehicles,
    ) {
    }

    public function create(User $user, StoreEmergencyGuidanceData $data): EmergencyGuidanceRequest
    {
        if ($data->vehicleId !== null && ! $this->vehicles->findForUser($user->id, $data->vehicleId)) {
            throw ValidationException::withMessages([
                'vehicle_id' => __('The selected vehicle was not found.'),
            ]);
        }

        $urgency = $this->hasHighRiskKeyword($data) ? SosUrgency::High : SosUrgency::Medium;
        $needsSos = $urgency === SosUrgency::High;
        $recommendedSosType = $this->recommendedSosType($data);
        $aiResponse = $this->buildSafeGuidance($data, $urgency, $needsSos, $recommendedSosType);

        return DB::transaction(fn (): EmergencyGuidanceRequest => $this->guidanceRequests->createForUser(
            $user->id,
            $data,
            $aiResponse,
            $urgency,
            $needsSos,
            $recommendedSosType,
            self::SAFETY_MESSAGE
        ));
    }

    private function hasHighRiskKeyword(StoreEmergencyGuidanceData $data): bool
    {
        $text = $this->searchableText($data);

        foreach (self::HIGH_RISK_KEYWORDS as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function recommendedSosType(StoreEmergencyGuidanceData $data): ?SosServiceType
    {
        $text = $this->searchableText($data);
        $slug = 'towing';

        if (str_contains($text, 'battery') || str_contains($text, 'electrical')) {
            $slug = 'electrical-emergency';
        }

        if (str_contains($text, 'not starting')) {
            $slug = 'car-not-starting';
        }

        if (str_contains($text, 'tire') || str_contains($text, 'tyre')) {
            $slug = 'flat-tire';
        }

        if (str_contains($text, 'overheating')) {
            $slug = 'overheating';
        }

        if (str_contains($text, 'brake') || str_contains($text, 'brakes')) {
            $slug = 'brake-emergency';
        }

        if (str_contains($text, 'accident') || str_contains($text, 'fire') || str_contains($text, 'smoke')) {
            $slug = 'accident-support';
        }

        return $this->sosServiceTypes->findActiveBySlug($slug)
            ?? $this->sosServiceTypes->findActiveBySlug('towing');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSafeGuidance(
        StoreEmergencyGuidanceData $data,
        SosUrgency $urgency,
        bool $needsSos,
        ?SosServiceType $recommendedSosType
    ): array {
        $steps = [
            'Stop in a safe place if you can do so without increasing risk.',
            'Keep passengers away from traffic and visible hazards.',
            'Do not attempt roadside mechanical repairs.',
            'Use SOS support or local emergency services if there is immediate danger.',
            'Wait for a qualified professional before inspecting the vehicle.',
        ];

        if ($urgency === SosUrgency::High) {
            array_unshift($steps, 'Treat this as high risk and prioritize personal safety.');
        }

        return [
            'urgency' => $urgency->value,
            'needs_sos' => $needsSos,
            'recommended_sos_service_type' => $recommendedSosType?->name,
            'safe_steps' => $steps,
            'avoid' => [
                'Do not open hot engine components.',
                'Do not drive if braking, steering, smoke, fire, overheating, or accident risk is present.',
                'Do not touch exposed wires, leaking fluids, or damaged parts.',
            ],
            'location' => [
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
            ],
        ];
    }

    private function searchableText(StoreEmergencyGuidanceData $data): string
    {
        return strtolower($data->description . ' ' . implode(' ', $data->symptoms ?? []));
    }
}
