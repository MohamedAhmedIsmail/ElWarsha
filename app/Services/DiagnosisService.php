<?php

namespace App\Services;

use App\DTOs\Diagnosis\DiagnosisSuggestionData;
use App\DTOs\Diagnosis\DummyDiagnosisResult;
use App\DTOs\Diagnosis\StoreDiagnosisData;
use App\DTOs\Diagnosis\UploadDiagnosisMediaData;
use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisUrgency;
use App\Models\Diagnosis;
use App\Models\DiagnosisMedia;
use App\Models\User;
use App\Models\Workshop;
use App\Repositories\Contracts\DiagnosisCategoryRepositoryInterface;
use App\Repositories\Contracts\DiagnosisMediaRepositoryInterface;
use App\Repositories\Contracts\DiagnosisRepositoryInterface;
use App\Repositories\Contracts\DiagnosisWorkshopSuggestionRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DiagnosisService
{
    private const SAFETY_DISCLAIMER = 'This is an initial AI-assisted diagnosis and is not a replacement for a professional inspection.';

    public function __construct(
        private readonly DiagnosisRepositoryInterface $diagnoses,
        private readonly DiagnosisMediaRepositoryInterface $media,
        private readonly DiagnosisWorkshopSuggestionRepositoryInterface $suggestions,
        private readonly DiagnosisCategoryRepositoryInterface $categories,
        private readonly VehicleRepositoryInterface $vehicles,
    ) {
    }

    /**
     * @return Collection<int, Diagnosis>
     */
    public function listForUser(User $user): Collection
    {
        return $this->diagnoses->listForUser($user->id);
    }

    public function getForUser(User $user, int $diagnosisId): Diagnosis
    {
        return $this->diagnoses->findForUser($user->id, $diagnosisId)
            ?? throw new NotFoundHttpException('Diagnosis not found.');
    }

    public function create(User $user, StoreDiagnosisData $data): Diagnosis
    {
        if (! $this->vehicles->findForUser($user->id, $data->vehicleId)) {
            throw ValidationException::withMessages([
                'vehicle_id' => __('The selected vehicle was not found.'),
            ]);
        }

        return DB::transaction(function () use ($user, $data): Diagnosis {
            $diagnosis = $this->diagnoses->createPending($user->id, $data);

            return $this->diagnoses->complete($diagnosis, $this->runDummyDiagnosis($data->description));
        });
    }

    /**
     * @return Collection<int, DiagnosisMedia>
     */
    public function uploadMedia(User $user, int $diagnosisId, UploadDiagnosisMediaData $data): Collection
    {
        $diagnosis = $this->getForUser($user, $diagnosisId);

        return DB::transaction(function () use ($diagnosis, $data): Collection {
            $created = new Collection();

            foreach ($data->files as $file) {
                $path = $file->store("diagnoses/{$diagnosis->id}", 'public');
                $created->push($this->media->create($diagnosis, $data->mediaType, $path));
            }

            return $created;
        });
    }

    /**
     * @return Collection<int, \App\Models\DiagnosisWorkshopSuggestion>
     */
    public function recommendedWorkshops(User $user, int $diagnosisId, DiagnosisSuggestionData $data): Collection
    {
        $diagnosis = $this->getForUser($user, $diagnosisId);

        DB::transaction(function () use ($diagnosis, $data): void {
            $this->suggestions->clearForDiagnosis($diagnosis);

            foreach ($this->suggestions->findCandidateWorkshops($diagnosis, $data) as $workshop) {
                $this->suggestions->create(
                    $diagnosis,
                    $workshop->id,
                    $this->scoreWorkshop($workshop),
                    $this->suggestionReason($diagnosis, $workshop, $data)
                );
            }
        });

        return $this->suggestions->listForDiagnosis($diagnosis);
    }

    private function runDummyDiagnosis(string $description): DummyDiagnosisResult
    {
        $descriptionLower = strtolower($description);

        if (str_contains($descriptionLower, 'battery') || str_contains($descriptionLower, 'not starting') || str_contains($descriptionLower, 'clicking')) {
            return $this->result(
                'Electricity',
                'Possible battery or alternator issue',
                'Check battery voltage and alternator charging output.',
                DiagnosisUrgency::Medium
            );
        }

        if (str_contains($descriptionLower, 'brake')) {
            return $this->result(
                'Brakes',
                'Possible brake system issue',
                'Inspect brake pads, brake fluid level, and hydraulic system.',
                DiagnosisUrgency::High
            );
        }

        if (str_contains($descriptionLower, 'overheating')) {
            $category = $this->categories->findActiveByName('Mechanics') ?? $this->categories->findActiveByName('AC');

            return $this->buildResult(
                $category,
                'Possible cooling system issue',
                'Check coolant level, radiator fan operation, thermostat, and visible leaks.',
                DiagnosisUrgency::High
            );
        }

        return $this->result(
            'Mechanics',
            'Possible mechanical issue',
            'Start with a professional inspection of fluids, belts, sensors, and visible leaks.',
            DiagnosisUrgency::Medium
        );
    }

    private function result(string $categoryName, string $diagnosis, string $technicalNote, DiagnosisUrgency $urgency): DummyDiagnosisResult
    {
        return $this->buildResult(
            $this->categories->findActiveByName($categoryName),
            $diagnosis,
            $technicalNote,
            $urgency
        );
    }

    private function buildResult($category, string $diagnosis, string $technicalNote, DiagnosisUrgency $urgency): DummyDiagnosisResult
    {
        $aiResponse = [
            'diagnosis' => $diagnosis,
            'confidence' => DiagnosisConfidence::Medium->value,
            'urgency' => $urgency->value,
            'affected_category' => $category?->name,
            'technical_note' => $technicalNote,
            'recommend_professional' => true,
            'safety_disclaimer' => self::SAFETY_DISCLAIMER,
        ];

        return new DummyDiagnosisResult(
            aiResponse: $aiResponse,
            diagnosisText: $diagnosis,
            confidence: DiagnosisConfidence::Medium,
            urgency: $urgency,
            affectedCategory: $category,
            recommendProfessional: true,
        );
    }

    private function scoreWorkshop(Workshop $workshop): float
    {
        $score = ((float) $workshop->rating_avg * 10) + ($workshop->is_verified ? 25 : 0);

        if (isset($workshop->distance)) {
            $score += max(0, 25 - ((float) $workshop->distance * 2));
        }

        return round(min(100, $score), 2);
    }

    private function suggestionReason(Diagnosis $diagnosis, Workshop $workshop, DiagnosisSuggestionData $data): string
    {
        $category = $diagnosis->affectedCategory?->name ?? 'the affected category';
        $parts = ["Offers services for {$category}"];

        if ($workshop->is_verified) {
            $parts[] = 'verified workshop';
        }

        if ((float) $workshop->rating_avg > 0) {
            $parts[] = 'rated ' . $workshop->rating_avg . '/5';
        }

        if ($data->lat !== null && $data->lng !== null && isset($workshop->distance)) {
            $parts[] = round((float) $workshop->distance, 2) . ' km away';
        }

        return implode(', ', $parts) . '.';
    }
}
