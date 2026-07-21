<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Diagnosis\DiagnosisSuggestionData;
use App\Models\Diagnosis;
use App\Models\DiagnosisWorkshopSuggestion;
use App\Models\Workshop;
use App\Repositories\Contracts\DiagnosisWorkshopSuggestionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

class DiagnosisWorkshopSuggestionRepository implements DiagnosisWorkshopSuggestionRepositoryInterface
{
    public function findCandidateWorkshops(Diagnosis $diagnosis, DiagnosisSuggestionData $data): BaseCollection
    {
        if (! $diagnosis->affected_category_id) {
            return collect();
        }

        $query = Workshop::query()
            ->approved()
            ->with(['services.category', 'brands', 'images'])
            ->whereHas('services', function ($query) use ($diagnosis): void {
                $query->where('services.service_category_id', $diagnosis->affected_category_id);
            })
            ->select('workshops.*');

        if ($data->lat !== null && $data->lng !== null) {
            $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(workshops.latitude)) * cos(radians(workshops.longitude) - radians(?)) + sin(radians(?)) * sin(radians(workshops.latitude))))';
            $query->selectRaw($haversine . ' AS distance', [$data->lat, $data->lng, $data->lat])
                ->orderBy('distance');
        }

        return $query->orderByDesc('workshops.is_verified')
            ->orderByDesc('workshops.rating_avg')
            ->limit($data->limit)
            ->get();
    }

    public function clearForDiagnosis(Diagnosis $diagnosis): void
    {
        $diagnosis->suggestions()->delete();
    }

    public function create(Diagnosis $diagnosis, int $workshopId, float $score, string $reason): DiagnosisWorkshopSuggestion
    {
        return $diagnosis->suggestions()->create([
            'workshop_id' => $workshopId,
            'score' => $score,
            'reason' => $reason,
        ]);
    }

    public function listForDiagnosis(Diagnosis $diagnosis): Collection
    {
        return $diagnosis->suggestions()
            ->with(['workshop.services.category', 'workshop.brands', 'workshop.images'])
            ->orderByDesc('score')
            ->get();
    }
}
