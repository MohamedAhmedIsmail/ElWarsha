<?php

namespace App\Models;

use App\Enums\DiagnosisConfidence;
use App\Enums\DiagnosisStatus;
use App\Enums\DiagnosisUrgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'description',
        'symptoms_json',
        'ai_response',
        'diagnosis_text',
        'confidence',
        'urgency',
        'affected_category_id',
        'recommend_professional',
        'status',
        'disclaimer_accepted',
    ];

    protected $casts = [
        'symptoms_json' => 'array',
        'ai_response' => 'array',
        'confidence' => DiagnosisConfidence::class,
        'urgency' => DiagnosisUrgency::class,
        'recommend_professional' => 'boolean',
        'status' => DiagnosisStatus::class,
        'disclaimer_accepted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function affectedCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'affected_category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(DiagnosisMedia::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(DiagnosisWorkshopSuggestion::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('diagnoses.user_id', $userId);
    }
}
