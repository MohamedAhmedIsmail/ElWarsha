<?php

namespace App\Services;

use App\DTOs\Crm\LeadFilterData;
use App\DTOs\Crm\StoreLeadNoteData;
use App\DTOs\Crm\UpdateLeadStatusData;
use App\Enums\LeadSource;
use App\Enums\WorkshopAnalyticsEventType;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\User;
use App\Models\Workshop;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkshopCrmService
{
    public function __construct(
        private readonly WorkshopRepositoryInterface $workshops,
        private readonly LeadRepositoryInterface $leads,
        private readonly WorkshopAnalyticsRepositoryInterface $analytics,
    ) {
    }

    /**
     * @return Collection<int, Lead>
     */
    public function listLeads(User $owner, LeadFilterData $filters): Collection
    {
        return $this->leads->listForWorkshop($this->getOwnedWorkshop($owner), $filters);
    }

    public function getLead(User $owner, int $leadId): Lead
    {
        return $this->findLeadForOwner($owner, $leadId);
    }

    public function updateLeadStatus(User $owner, int $leadId, UpdateLeadStatusData $data): Lead
    {
        $lead = $this->findLeadForOwner($owner, $leadId);
        $oldStatus = $lead->status;

        return DB::transaction(function () use ($lead, $oldStatus, $data, $owner): Lead {
            $updated = $this->leads->updateStatus($lead, $data->status);
            $this->leads->createStatusLog($updated, $oldStatus, $data->status, $owner->id);

            return $updated->refresh()->load(['user', 'vehicle.brand', 'vehicle.model', 'booking', 'diagnosis', 'sosRequest', 'leadNotes.user', 'statusLogs.changedBy']);
        });
    }

    public function addLeadNote(User $owner, int $leadId, StoreLeadNoteData $data): LeadNote
    {
        $lead = $this->findLeadForOwner($owner, $leadId);

        return $this->leads->createNote($lead, $owner->id, $data->note);
    }

    /**
     * @return array<string, mixed>
     */
    public function analytics(User $owner): array
    {
        $workshop = $this->getOwnedWorkshop($owner);
        $bySource = $this->leads->countsBySource($workshop);
        $byStatus = $this->leads->countsByStatus($workshop);
        $byEvent = $this->analytics->countsByEventType($workshop);

        return [
            'total_leads' => array_sum($bySource),
            'leads_by_source' => $bySource,
            'leads_by_status' => $byStatus,
            'call_clicks_count' => $byEvent[WorkshopAnalyticsEventType::CallClick->value] ?? 0,
            'whatsapp_clicks_count' => $byEvent[WorkshopAnalyticsEventType::WhatsappClick->value] ?? 0,
            'directions_clicks_count' => $byEvent[WorkshopAnalyticsEventType::DirectionsClick->value] ?? 0,
            'bookings_count' => $bySource[LeadSource::Booking->value] ?? 0,
            'sos_leads_count' => $bySource[LeadSource::Sos->value] ?? 0,
        ];
    }

    private function findLeadForOwner(User $owner, int $leadId): Lead
    {
        $workshop = $this->getOwnedWorkshop($owner);

        return $this->leads->findForWorkshop($workshop, $leadId)
            ?? throw new NotFoundHttpException('Lead not found.');
    }

    private function getOwnedWorkshop(User $owner): Workshop
    {
        return $this->workshops->findForOwner($owner->id)
            ?? throw new NotFoundHttpException('Workshop profile not found.');
    }
}
