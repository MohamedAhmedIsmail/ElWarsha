<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'workshop_id',
        'booking_id',
        'sos_request_id',
        'rating',
        'quality_rating',
        'price_rating',
        'punctuality_rating',
        'behavior_rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'quality_rating' => 'integer',
        'price_rating' => 'integer',
        'punctuality_rating' => 'integer',
        'behavior_rating' => 'integer',
        'status' => ReviewStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function sosRequest(): BelongsTo
    {
        return $this->belongsTo(SosRequest::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('reviews.status', ReviewStatus::Published->value);
    }
}
