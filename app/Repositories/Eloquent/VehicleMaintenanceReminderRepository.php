<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Maintenance\StoreMaintenanceReminderData;
use App\DTOs\Maintenance\UpdateMaintenanceReminderData;
use App\Enums\MaintenanceReminderStatus;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceReminder;
use App\Repositories\Contracts\VehicleMaintenanceReminderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class VehicleMaintenanceReminderRepository implements VehicleMaintenanceReminderRepositoryInterface
{
    public function listForVehicle(Vehicle $vehicle): Collection
    {
        return $vehicle->maintenanceReminders()
            ->with($this->relations())
            ->latest('id')
            ->get();
    }

    public function findForUser(int $userId, int $reminderId): ?VehicleMaintenanceReminder
    {
        return VehicleMaintenanceReminder::query()
            ->ownedBy($userId)
            ->with($this->relations())
            ->whereKey($reminderId)
            ->first();
    }

    public function upcomingForUser(int $userId, int $days): Collection
    {
        $until = now()->addDays($days)->toDateString();

        return VehicleMaintenanceReminder::query()
            ->ownedBy($userId)
            ->active()
            ->with($this->relations())
            ->where(function (Builder $query) use ($until): void {
                $query->whereDate('next_due_at', '<=', $until)
                    ->orWhereHas('vehicle', function (Builder $query): void {
                        $query->whereColumn('vehicle_maintenance_reminders.next_due_mileage', '<=', 'vehicles.mileage_km');
                    });
            })
            ->orderByRaw('CASE WHEN next_due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('next_due_at')
            ->latest('id')
            ->get();
    }

    public function createForVehicle(int $userId, Vehicle $vehicle, StoreMaintenanceReminderData $data): VehicleMaintenanceReminder
    {
        $reminder = VehicleMaintenanceReminder::query()->create([
            ...$data->toArray(),
            'user_id' => $userId,
            'vehicle_id' => $vehicle->id,
        ]);

        return $reminder->load($this->relations());
    }

    public function update(VehicleMaintenanceReminder $reminder, UpdateMaintenanceReminderData $data): VehicleMaintenanceReminder
    {
        $reminder->forceFill($data->toArray())->save();

        return $reminder->refresh()->load($this->relations());
    }

    public function delete(VehicleMaintenanceReminder $reminder): void
    {
        $reminder->delete();
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'vehicle.brand',
            'vehicle.model',
            'maintenanceItem.serviceCategory',
        ];
    }
}
