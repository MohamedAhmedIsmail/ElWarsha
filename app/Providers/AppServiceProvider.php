<?php

namespace App\Providers;

use App\Repositories\Contracts\OtpCodeRepositoryInterface;
use App\Repositories\Contracts\CarBrandRepositoryInterface;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;
use App\Repositories\Contracts\WorkshopImageRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use App\Repositories\Contracts\WorkshopWorkingHourRepositoryInterface;
use App\Repositories\Eloquent\OtpCodeRepository;
use App\Repositories\Eloquent\CarBrandRepository;
use App\Repositories\Eloquent\CarModelRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\WorkshopAnalyticsRepository;
use App\Repositories\Eloquent\WorkshopImageRepository;
use App\Repositories\Eloquent\WorkshopRepository;
use App\Repositories\Eloquent\WorkshopWorkingHourRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(OtpCodeRepositoryInterface::class, OtpCodeRepository::class);
        $this->app->bind(CarBrandRepositoryInterface::class, CarBrandRepository::class);
        $this->app->bind(CarModelRepositoryInterface::class, CarModelRepository::class);
        $this->app->bind(ServiceCategoryRepositoryInterface::class, ServiceCategoryRepository::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
        $this->app->bind(VehicleRepositoryInterface::class, VehicleRepository::class);
        $this->app->bind(WorkshopRepositoryInterface::class, WorkshopRepository::class);
        $this->app->bind(WorkshopImageRepositoryInterface::class, WorkshopImageRepository::class);
        $this->app->bind(WorkshopWorkingHourRepositoryInterface::class, WorkshopWorkingHourRepository::class);
        $this->app->bind(ReviewRepositoryInterface::class, ReviewRepository::class);
        $this->app->bind(WorkshopAnalyticsRepositoryInterface::class, WorkshopAnalyticsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
    }
}
