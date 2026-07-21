<?php

namespace App\Repositories\Contracts;

use App\DTOs\Diagnosis\DummyDiagnosisResult;
use App\DTOs\Diagnosis\StoreDiagnosisData;
use App\Models\Diagnosis;
use Illuminate\Database\Eloquent\Collection;

interface DiagnosisRepositoryInterface
{
    /**
     * @return Collection<int, Diagnosis>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $diagnosisId): ?Diagnosis;

    public function createPending(int $userId, StoreDiagnosisData $data): Diagnosis;

    public function complete(Diagnosis $diagnosis, DummyDiagnosisResult $result): Diagnosis;
}
