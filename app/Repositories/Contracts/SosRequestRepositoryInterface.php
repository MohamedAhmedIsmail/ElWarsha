<?php

namespace App\Repositories\Contracts;

use App\DTOs\Sos\StoreSosRequestData;
use App\Enums\SosRequestStatus;
use App\Models\SosProvider;
use App\Models\SosRequest;
use Illuminate\Database\Eloquent\Collection;

interface SosRequestRepositoryInterface
{
    /**
     * @return Collection<int, SosRequest>
     */
    public function listForUser(int $userId): Collection;

    public function findForUser(int $userId, int $sosRequestId): ?SosRequest;

    /**
     * @return Collection<int, SosRequest>
     */
    public function listForProvider(SosProvider $provider): Collection;

    public function findForProvider(SosProvider $provider, int $sosRequestId): ?SosRequest;

    public function createForUser(int $userId, StoreSosRequestData $data, ?string $imagePath): SosRequest;

    public function assignProvider(SosRequest $sosRequest, SosProvider $provider): SosRequest;

    public function updateStatus(SosRequest $sosRequest, SosRequestStatus $status): SosRequest;

    public function unassignToPending(SosRequest $sosRequest): SosRequest;
}
