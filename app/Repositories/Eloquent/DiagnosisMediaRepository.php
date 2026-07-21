<?php

namespace App\Repositories\Eloquent;

use App\Enums\DiagnosisMediaType;
use App\Models\Diagnosis;
use App\Models\DiagnosisMedia;
use App\Repositories\Contracts\DiagnosisMediaRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DiagnosisMediaRepository implements DiagnosisMediaRepositoryInterface
{
    public function create(Diagnosis $diagnosis, DiagnosisMediaType $mediaType, string $path): DiagnosisMedia
    {
        return $diagnosis->media()->create([
            'media_type' => $mediaType,
            'file_path' => $path,
        ]);
    }

    public function listForDiagnosis(Diagnosis $diagnosis): Collection
    {
        return $diagnosis->media()
            ->latest('id')
            ->get();
    }
}
