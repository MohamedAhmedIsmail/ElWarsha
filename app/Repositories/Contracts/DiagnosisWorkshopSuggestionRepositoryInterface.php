<?php

namespace App\Repositories\Contracts;

use App\DTOs\Diagnosis\DiagnosisSuggestionData;
use App\Models\Diagnosis;
use App\Models\DiagnosisWorkshopSuggestion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as BaseCollection;

interface DiagnosisWorkshopSuggestionRepositoryInterface
{
    /**
     * @return BaseCollection<int, \App\Models\Workshop>
     */
    public function findCandidateWorkshops(Diagnosis $diagnosis, DiagnosisSuggestionData $data): BaseCollection;

    public function clearForDiagnosis(Diagnosis $diagnosis): void;

    public function create(Diagnosis $diagnosis, int $workshopId, float $score, string $reason): DiagnosisWorkshopSuggestion;

    /**
     * @return Collection<int, DiagnosisWorkshopSuggestion>
     */
    public function listForDiagnosis(Diagnosis $diagnosis): Collection;
}
