<?php

namespace App\Models;

use App\Enums\WorkshopStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SosProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'workshop_id', 'name', 'phone', 'whatsapp', 'city', 'area',
        'latitude', 'longitude', 'is_available', 'rating_avg', 'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_available' => 'boolean',
        'rating_avg' => 'decimal:2',
        'status' => WorkshopStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(SosServiceType::class, 'sos_provider_services');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(SosRequest::class, 'assigned_provider_id');
    }

    public function scopeApprovedAvailable(Builder $query): Builder
    {
        return $query
            ->where('sos_providers.status', WorkshopStatus::Approved->value)
            ->where('sos_providers.is_available', true);
    }
}
