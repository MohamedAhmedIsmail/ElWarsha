<?php

namespace App\Repositories\Eloquent;

use App\Models\Workshop;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function listPublishedForWorkshop(Workshop $workshop, int $perPage): LengthAwarePaginator
    {
        return $workshop->reviews()
            ->published()
            ->with(['user:id,name,phone,avatar,city,area,role,status,phone_verified_at,last_login_at,created_at'])
            ->latest('id')
            ->paginate($perPage);
    }
}
