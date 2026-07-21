<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'changed_by',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
