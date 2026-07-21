<?php

namespace App\Models;

use App\Enums\WorkshopStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workshop extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'phone',
        'whatsapp',
        'email',
        'address',
        'city',
        'area',
        'latitude',
        'longitude',
        'google_maps_url',
        'accepts_booking',
        'accepts_sos',
        'is_verified',
        'rating_avg',
        'reviews_count',
        'status',
        'subscription_status',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accepts_booking' => 'boolean',
        'accepts_sos' => 'boolean',
        'is_verified' => 'boolean',
        'rating_avg' => 'decimal:2',
        'reviews_count' => 'integer',
        'status' => WorkshopStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'workshop_services');
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(CarBrand::class, 'workshop_brands');
    }

    public function images(): HasMany
    {
        return $this->hasMany(WorkshopImage::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(WorkshopWorkingHour::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(WorkshopAnalyticsEvent::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function sosProviders(): HasMany
    {
        return $this->hasMany(SosProvider::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('workshops.status', WorkshopStatus::Approved->value);
    }

    public function scopeOwnedBy(Builder $query, int $ownerId): Builder
    {
        return $query->where('workshops.owner_id', $ownerId);
    }
}
