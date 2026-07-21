<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SosServiceType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'icon', 'status'];

    protected $casts = [
        'status' => RecordStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(SosProvider::class, 'sos_provider_services');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('sos_service_types.status', RecordStatus::Active->value);
    }
}
