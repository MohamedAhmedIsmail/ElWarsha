<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopWorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'day_of_week',
        'opens_at',
        'closes_at',
        'is_closed',
    ];

    protected $casts = [
        'day_of_week' => DayOfWeek::class,
        'is_closed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}
