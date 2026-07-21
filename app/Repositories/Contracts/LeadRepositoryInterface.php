<?php

namespace App\Repositories\Contracts;

use App\DTOs\Crm\LeadFilterData;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\SosRequest;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Collection;

interface LeadRepositoryInterface
{
    /**
     * @return Collection<int, Lead>
     */
    public function listForWorkshop(Workshop $workshop, LeadFilterData $filters): Collection;

    public function findForWorkshop(Workshop $workshop, int $leadId): ?Lead;

    public function createFromTracking(Workshop $workshop, ?int $userId, ?int $vehicleId, LeadSource $source): Lead;

    public function hasProfileViewLeadToday(Workshop $workshop, int $userId): bool;

    public function createFromBooking(Booking $booking): Lead;

    public function createFromSosRequest(SosRequest $sosRequest): Lead;

    public function updateStatus(Lead $lead, LeadStatus $status): Lead;

    public function createStatusLog(Lead $lead, ?LeadStatus $oldStatus, LeadStatus $newStatus, int $changedBy): void;

    public function createNote(Lead $lead, int $userId, string $note): LeadNote;

    /**
     * @return array<string, int>
     */
    public function countsBySource(Workshop $workshop): array;

    /**
     * @return array<string, int>
     */
    public function countsByStatus(Workshop $workshop): array;
}
