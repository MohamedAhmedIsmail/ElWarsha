<?php

namespace App\Models;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
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
}
