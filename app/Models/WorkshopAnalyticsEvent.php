<?php

namespace App\Models;

use App\Enums\WorkshopAnalyticsEventType;
use Illuminate\Database\Eloquent\Model;

class WorkshopAnalyticsEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'workshop_id',
        'user_id',
        'event_type',
        'latitude',
        'longitude',
        'metadata',
    ];

    protected $casts = [
        'event_type' => WorkshopAnalyticsEventType::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];
}
