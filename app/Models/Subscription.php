<?php

namespace App\Models;

use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'auto_renew',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'status' => SubscriptionStatus::class,
        'auto_renew' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
