<?php

namespace App\Repositories\Contracts;

use App\Enums\DiagnosisMediaType;
use App\Models\Diagnosis;
use App\Models\DiagnosisMedia;
use Illuminate\Database\Eloquent\Collection;

interface DiagnosisMediaRepositoryInterface
{
    public function create(Diagnosis $diagnosis, DiagnosisMediaType $mediaType, string $path): DiagnosisMedia;

    /**
     * @return Collection<int, DiagnosisMedia>
     */
    public function listForDiagnosis(Diagnosis $diagnosis): Collection;
}
