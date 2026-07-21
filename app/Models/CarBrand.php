<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarBrand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => RecordStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(CarModel::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->getTable() . '.status', RecordStatus::Active->value);
    }
}
