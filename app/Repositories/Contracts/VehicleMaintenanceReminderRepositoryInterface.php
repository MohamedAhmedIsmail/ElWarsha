<?php

namespace App\Repositories\Contracts;

use App\DTOs\Maintenance\StoreMaintenanceReminderData;
use App\DTOs\Maintenance\UpdateMaintenanceReminderData;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceReminder;
use Illuminate\Database\Eloquent\Collection;

interface VehicleMaintenanceReminderRepositoryInterface
{
    /**
     * @return Collection<int, VehicleMaintenanceReminder>
     */
    public function listForVehicle(Vehicle $vehicle): Collection;

    public function findForUser(int $userId, int $reminderId): ?VehicleMaintenanceReminder;

    /**
     * @return Collection<int, VehicleMaintenanceReminder>
     */
    public function upcomingForUser(int $userId, int $days): Collection;

    public function createForVehicle(int $userId, Vehicle $vehicle, StoreMaintenanceReminderData $data): VehicleMaintenanceReminder;

    public function update(VehicleMaintenanceReminder $reminder, UpdateMaintenanceReminderData $data): VehicleMaintenanceReminder;

    public function delete(VehicleMaintenanceReminder $reminder): void;
}
