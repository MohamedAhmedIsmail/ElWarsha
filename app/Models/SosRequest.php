<?php

namespace App\Models;

use App\Enums\SosRequestStatus;
use App\Enums\SosUrgency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SosRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vehicle_id', 'sos_service_type_id', 'assigned_provider_id',
        'description', 'image_path', 'latitude', 'longitude', 'urgency',
        'status', 'accepted_at', 'arrived_at', 'completed_at', 'cancelled_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'urgency' => SosUrgency::class,
        'status' => SosRequestStatus::class,
        'accepted_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
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

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(SosServiceType::class, 'sos_service_type_id');
    }

    public function assignedProvider(): BelongsTo
    {
        return $this->belongsTo(SosProvider::class, 'assigned_provider_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(SosRequestLog::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('sos_requests.user_id', $userId);
    }
}
