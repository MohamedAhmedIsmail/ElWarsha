<?php

namespace App\Services;

use App\DTOs\Booking\BookingStatusChangeData;
use App\DTOs\Booking\StoreBookingData;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use App\Models\Workshop;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookingStatusLogRepositoryInterface;
use App\Repositories\Contracts\DiagnosisRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\WhatsappMessageRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingService
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $allowedTransitions = [
        BookingStatus::Pending->value => [BookingStatus::Accepted->value, BookingStatus::Declined->value, BookingStatus::Cancelled->value],
        BookingStatus::Accepted->value => [BookingStatus::InProgress->value],
        BookingStatus::InProgress->value => [BookingStatus::Completed->value],
    ];

    public function __construct(
        private readonly BookingRepositoryInterface $bookings,
        private readonly BookingStatusLogRepositoryInterface $statusLogs,
        private readonly VehicleRepositoryInterface $vehicles,
        private readonly DiagnosisRepositoryInterface $diagnoses,
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly LeadRepositoryInterface $leads,
        private readonly NotificationRepositoryInterface $notifications,
        private readonly WhatsappMessageRepositoryInterface $whatsappMessages,
        private readonly ServiceLedgerRepositoryInterface $serviceLedgers,
    ) {
    }

    /**
     * @return Collection<int, Booking>
     */
    public function listForUser(User $user): Collection
    {
        return $this->bookings->listForUser($user->id);
    }

    public function getForUser(User $user, int $bookingId): Booking
    {
        return $this->bookings->findForUser($user->id, $bookingId)
            ?? throw new NotFoundHttpException('Booking not found.');
    }

    public function create(User $user, StoreBookingData $data): Booking
    {
        $vehicle = $this->vehicles->findForUser($user->id, $data->vehicleId);
        if (! $vehicle) {
            throw ValidationException::withMessages(['vehicle_id' => __('The selected vehicle was not found.')]);
        }

        $workshop = $this->workshops->findApproved($data->workshopId);
        if (! $workshop || ! $workshop->accepts_booking) {
            throw ValidationException::withMessages(['workshop_id' => __('The selected workshop is not available for booking.')]);
        }

        if ($data->diagnosisId && ! $this->diagnoses->findForUser($user->id, $data->diagnosisId)) {
            throw ValidationException::withMessages(['diagnosis_id' => __('The selected diagnosis was not found.')]);
        }

        if ($data->serviceId && ! $workshop->services->contains('id', $data->serviceId)) {
            throw ValidationException::withMessages(['service_id' => __('The selected service is not offered by this workshop.')]);
        }

        return DB::transaction(function () use ($user, $data): Booking {
            $booking = $this->bookings->createForUser($user->id, $data);
            $this->statusLogs->create($booking, null, BookingStatus::Pending, $user->id, 'Booking created.');
            $this->leads->createFromBooking($booking);

            if ($booking->workshop->owner_id) {
                $this->notifications->create(
                    $booking->workshop->owner_id,
                    'New booking request',
                    'You have a new booking request #' . $booking->id . '.',
                    'booking_created',
                    ['booking_id' => $booking->id]
                );
            }

            $this->whatsappMessages->createWorkshopBookingNotification($booking);

            return $booking->refresh()->load(['vehicle.brand', 'vehicle.model', 'workshop', 'diagnosis.affectedCategory', 'service.category', 'statusLogs']);
        });
    }

    public function cancel(User $user, int $bookingId, BookingStatusChangeData $data): Booking
    {
        return DB::transaction(fn (): Booking => $this->transition(
            $this->getForUser($user, $bookingId),
            BookingStatus::Cancelled,
            $user,
            $data->notes
        ));
    }

    /**
     * @return Collection<int, Booking>
     */
    public function listForWorkshopOwner(User $owner): Collection
    {
        return $this->bookings->listForWorkshop($this->ownedWorkshop($owner));
    }

    public function getForWorkshopOwner(User $owner, int $bookingId): Booking
    {
        return $this->bookings->findForWorkshop($this->ownedWorkshop($owner), $bookingId)
            ?? throw new NotFoundHttpException('Booking not found.');
    }

    public function workshopTransition(User $owner, int $bookingId, BookingStatus $targetStatus, BookingStatusChangeData $data): Booking
    {
        return DB::transaction(function () use ($owner, $bookingId, $targetStatus, $data): Booking {
            $booking = $this->getForWorkshopOwner($owner, $bookingId);
            $updated = $this->transition($booking, $targetStatus, $owner, $data->notes);

            if ($targetStatus === BookingStatus::Completed) {
                $this->serviceLedgers->createFromCompletedBooking($updated);
            }

            if (in_array($targetStatus, [BookingStatus::Accepted, BookingStatus::Declined, BookingStatus::InProgress, BookingStatus::Completed], true)) {
                $this->notifications->create(
                    $updated->user_id,
                    'Booking status updated',
                    'Your booking #' . $updated->id . ' is now ' . $targetStatus->value . '.',
                    'booking_status_updated',
                    ['booking_id' => $updated->id, 'status' => $targetStatus->value]
                );
            }

            return $updated;
        });
    }

    private function transition(Booking $booking, BookingStatus $targetStatus, User $actor, ?string $notes): Booking
    {
        $oldStatus = $booking->status instanceof BookingStatus ? $booking->status : BookingStatus::from($booking->status);

        if (! in_array($targetStatus->value, $this->allowedTransitions[$oldStatus->value] ?? [], true)) {
            throw ValidationException::withMessages([
                'status' => __('Invalid booking status transition.'),
            ]);
        }

        $updated = $this->bookings->updateStatus($booking, $targetStatus);
        $this->statusLogs->create($updated, $oldStatus, $targetStatus, $actor->id, $notes);

        return $updated;
    }

    private function ownedWorkshop(User $owner): Workshop
    {
        $role = $owner->role instanceof UserRole ? $owner->role : UserRole::from($owner->role);
        if ($role !== UserRole::WorkshopOwner) {
            throw new NotFoundHttpException('Workshop not found.');
        }

        return $this->workshops->findForOwner($owner->id)
            ?? throw new NotFoundHttpException('Workshop not found.');
    }
}
