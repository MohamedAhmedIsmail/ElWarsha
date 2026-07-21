<?php

namespace App\Repositories\Eloquent;

use App\DTOs\Crm\LeadFilterData;
use App\Enums\LeadSource;
use App\Enums\LeadStatus;
use App\Models\Booking;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\SosRequest;
use App\Models\Workshop;
use App\Repositories\Contracts\LeadRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class LeadRepository implements LeadRepositoryInterface
{
    public function listForWorkshop(Workshop $workshop, LeadFilterData $filters): Collection
    {
        return $workshop->leads()
            ->with($this->relations())
            ->when($filters->source, fn ($query, LeadSource $source) => $query->where('source', $source))
            ->when($filters->status, fn ($query, LeadStatus $status) => $query->where('status', $status))
            ->latest('id')
            ->get();
    }

    public function findForWorkshop(Workshop $workshop, int $leadId): ?Lead
    {
        return $workshop->leads()
            ->with($this->relations())
            ->whereKey($leadId)
            ->first();
    }

    public function createFromTracking(Workshop $workshop, ?int $userId, ?int $vehicleId, LeadSource $source): Lead
    {
        return Lead::query()->create([
            'workshop_id' => $workshop->id,
            'user_id' => $userId,
            'vehicle_id' => $vehicleId,
            'source' => $source,
            'status' => LeadStatus::New,
        ])->load($this->relations());
    }

    public function hasProfileViewLeadToday(Workshop $workshop, int $userId): bool
    {
        return Lead::query()
            ->where('workshop_id', $workshop->id)
            ->where('user_id', $userId)
            ->where('source', LeadSource::ProfileView)
            ->whereDate('created_at', now()->toDateString())
            ->exists();
    }

    public function createFromBooking(Booking $booking): Lead
    {
        return Lead::query()->create([
            'workshop_id' => $booking->workshop_id,
            'user_id' => $booking->user_id,
            'vehicle_id' => $booking->vehicle_id,
            'booking_id' => $booking->id,
            'diagnosis_id' => $booking->diagnosis_id,
            'source' => LeadSource::Booking,
            'status' => LeadStatus::New,
        ])->load($this->relations());
    }

    public function createFromSosRequest(SosRequest $sosRequest): Lead
    {
        return Lead::query()->create([
            'workshop_id' => $sosRequest->assignedProvider->workshop_id,
            'user_id' => $sosRequest->user_id,
            'vehicle_id' => $sosRequest->vehicle_id,
            'sos_request_id' => $sosRequest->id,
            'source' => LeadSource::Sos,
            'status' => LeadStatus::New,
        ])->load($this->relations());
    }

    public function updateStatus(Lead $lead, LeadStatus $status): Lead
    {
        $lead->forceFill(['status' => $status])->save();

        return $lead->refresh()->load($this->relations());
    }

    public function createStatusLog(Lead $lead, ?LeadStatus $oldStatus, LeadStatus $newStatus, int $changedBy): void
    {
        $lead->statusLogs()->create([
            'old_status' => $oldStatus?->value,
            'new_status' => $newStatus->value,
            'changed_by' => $changedBy,
        ]);
    }

    public function createNote(Lead $lead, int $userId, string $note): LeadNote
    {
        return $lead->leadNotes()->create([
            'user_id' => $userId,
            'note' => $note,
        ])->load('user');
    }

    public function countsBySource(Workshop $workshop): array
    {
        return $workshop->leads()
            ->selectRaw('source, COUNT(*) as aggregate')
            ->groupBy('source')
            ->pluck('aggregate', 'source')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    public function countsByStatus(Workshop $workshop): array
    {
        return $workshop->leads()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status')
            ->map(fn ($count) => (int) $count)
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return [
            'user',
            'vehicle.brand',
            'vehicle.model',
            'booking',
            'diagnosis',
            'sosRequest',
            'leadNotes.user',
            'statusLogs.changedBy',
        ];
    }
}
