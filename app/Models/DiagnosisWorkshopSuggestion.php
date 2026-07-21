<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosisWorkshopSuggestion extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'diagnosis_id',
        'workshop_id',
        'score',
        'reason',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
