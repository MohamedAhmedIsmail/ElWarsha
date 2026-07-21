<?php

namespace App\Models;

use App\Enums\SosUrgency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyGuidanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'description',
        'symptoms_json',
        'latitude',
        'longitude',
        'ai_response',
        'urgency',
        'needs_sos',
        'recommended_sos_service_type_id',
        'safety_message',
    ];

    protected $casts = [
        'symptoms_json' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ai_response' => 'array',
        'urgency' => SosUrgency::class,
        'needs_sos' => 'boolean',
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

    public function recommendedSosServiceType(): BelongsTo
    {
        return $this->belongsTo(SosServiceType::class, 'recommended_sos_service_type_id');
    }
}
