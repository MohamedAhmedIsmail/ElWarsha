<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'default_interval_km',
        'default_interval_months',
        'service_category_id',
        'status',
    ];

    protected $casts = [
        'default_interval_km' => 'integer',
        'default_interval_months' => 'integer',
        'status' => RecordStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceReminder::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('maintenance_items.status', RecordStatus::Active->value);
    }
}
