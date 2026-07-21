<?php

namespace App\Models;

use App\Enums\DiagnosisMediaType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisMedia extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'diagnosis_id',
        'media_type',
        'file_path',
    ];

    protected $casts = [
        'media_type' => DiagnosisMediaType::class,
        'created_at' => 'datetime',
    ];

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
