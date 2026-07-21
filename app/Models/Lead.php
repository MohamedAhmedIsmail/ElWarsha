<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'user_id',
        'vehicle_id',
        'booking_id',
        'diagnosis_id',
        'sos_request_id',
        'source',
        'status',
        'notes',
    ];

    protected $casts = [
        'source' => LeadSource::class,
        'status' => LeadStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function sosRequest(): BelongsTo
    {
        return $this->belongsTo(SosRequest::class);
    }

    public function leadNotes(): HasMany
    {
        return $this->hasMany(LeadNote::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(LeadStatusLog::class);
    }
}
