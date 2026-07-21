<?php

namespace App\Repositories\Eloquent;

use App\Models\SosProvider;
use App\Repositories\Contracts\SosProviderRepositoryInterface;

class SosProviderRepository implements SosProviderRepositoryInterface
{
    public function findNearestAvailable(int $serviceTypeId, float $latitude, float $longitude): ?SosProvider
    {
        $haversine = '(6371 * acos(cos(radians(?)) * cos(radians(sos_providers.latitude)) * cos(radians(sos_providers.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sos_providers.latitude))))';

        return SosProvider::query()
            ->approvedAvailable()
            ->with(['workshop', 'user', 'serviceTypes'])
            ->whereHas('serviceTypes', fn ($query) => $query->whereKey($serviceTypeId))
            ->select('sos_providers.*')
            ->selectRaw($haversine . ' AS distance', [$latitude, $longitude, $latitude])
            ->orderBy('distance')
            ->orderByDesc('sos_providers.rating_avg')
            ->first();
    }

    public function findForUser(int $userId): ?SosProvider
    {
        return SosProvider::query()
            ->where('user_id', $userId)
            ->with(['workshop', 'serviceTypes'])
            ->first();
    }
}
