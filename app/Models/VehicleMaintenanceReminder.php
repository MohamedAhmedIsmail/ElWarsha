<?php

namespace App\Models;

use App\Enums\MaintenanceReminderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenanceReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'maintenance_item_id',
        'last_done_at',
        'last_done_mileage',
        'next_due_at',
        'next_due_mileage',
        'reminder_before_days',
        'status',
        'notes',
    ];

    protected $casts = [
        'last_done_at' => 'date',
        'last_done_mileage' => 'integer',
        'next_due_at' => 'date',
        'next_due_mileage' => 'integer',
        'reminder_before_days' => 'integer',
        'status' => MaintenanceReminderStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceItem(): BelongsTo
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('vehicle_maintenance_reminders.user_id', $userId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('vehicle_maintenance_reminders.status', MaintenanceReminderStatus::Active->value);
    }
}
