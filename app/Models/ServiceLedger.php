<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLedger extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'workshop_id',
        'booking_id',
        'diagnosis_id',
        'sos_request_id',
        'maintenance_item_id',
        'title',
        'description',
        'service_date',
        'cost',
        'mileage_km',
        'invoice_file',
    ];

    protected $casts = [
        'service_date' => 'date',
        'cost' => 'decimal:2',
        'mileage_km' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function sosRequest(): BelongsTo
    {
        return $this->belongsTo(SosRequest::class);
    }

    public function maintenanceItem(): BelongsTo
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ServiceLedgerMedia::class);
    }
}
