<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'price',
        'duration_days',
        'description',
        'features',
        'is_featured',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'features' => 'array',
        'is_featured' => 'boolean',
        'status' => RecordStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('plans.status', RecordStatus::Active->value);
    }
}
