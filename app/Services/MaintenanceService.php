<?php

namespace App\Services;

use App\DTOs\Maintenance\StoreMaintenanceReminderData;
use App\DTOs\Maintenance\UpdateMaintenanceReminderData;
use App\Models\MaintenanceItem;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceReminder;
use App\Repositories\Contracts\MaintenanceItemRepositoryInterface;
use App\Repositories\Contracts\VehicleMaintenanceReminderRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MaintenanceService
{
    public function __construct(
        private readonly MaintenanceItemRepositoryInterface $items,
        private readonly VehicleMaintenanceReminderRepositoryInterface $reminders,
        private readonly VehicleRepositoryInterface $vehicles,
    ) {
    }

    /**
     * @return Collection<int, MaintenanceItem>
     */
    public function listItems(): Collection
    {
        return $this->items->listActive();
    }

    /**
     * @return Collection<int, VehicleMaintenanceReminder>
     */
    public function listVehicleReminders(User $user, int $vehicleId): Collection
    {
        return $this->reminders->listForVehicle($this->getVehicleForUser($user, $vehicleId));
    }

    public function createReminder(User $user, int $vehicleId, StoreMaintenanceReminderData $data): VehicleMaintenanceReminder
    {
        $vehicle = $this->getVehicleForUser($user, $vehicleId);
        $this->ensureActiveItem($data->maintenanceItemId);

        return $this->reminders->createForVehicle($user->id, $vehicle, $data);
    }

    public function updateReminder(User $user, int $reminderId, UpdateMaintenanceReminderData $data): VehicleMaintenanceReminder
    {
        return $this->reminders->update($this->getReminderForUser($user, $reminderId), $data);
    }

    public function deleteReminder(User $user, int $reminderId): void
    {
        $this->reminders->delete($this->getReminderForUser($user, $reminderId));
    }

    /**
     * @return Collection<int, VehicleMaintenanceReminder>
     */
    public function upcoming(User $user): Collection
    {
        return $this->reminders->upcomingForUser($user->id, 14);
    }

    private function getVehicleForUser(User $user, int $vehicleId): Vehicle
    {
        return $this->vehicles->findForUser($user->id, $vehicleId)
            ?? throw new NotFoundHttpException('Vehicle not found.');
    }

    private function getReminderForUser(User $user, int $reminderId): VehicleMaintenanceReminder
    {
        return $this->reminders->findForUser($user->id, $reminderId)
            ?? throw new NotFoundHttpException('Maintenance reminder not found.');
    }

    private function ensureActiveItem(int $maintenanceItemId): void
    {
        if (! $this->items->findActive($maintenanceItemId)) {
            throw ValidationException::withMessages([
                'maintenance_item_id' => __('The selected maintenance item was not found.'),
            ]);
        }
    }
}
