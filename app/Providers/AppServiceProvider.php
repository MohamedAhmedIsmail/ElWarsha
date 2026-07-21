<?php

namespace App\Providers;

use App\Repositories\Contracts\OtpCodeRepositoryInterface;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Repositories\Contracts\BookingStatusLogRepositoryInterface;
use App\Repositories\Contracts\CarBrandRepositoryInterface;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use App\Repositories\Contracts\EmergencyGuidanceRepositoryInterface;
use App\Repositories\Contracts\ServiceCategoryRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Repositories\Contracts\SosProviderRepositoryInterface;
use App\Repositories\Contracts\SosRequestLogRepositoryInterface;
use App\Repositories\Contracts\SosRequestRepositoryInterface;
use App\Repositories\Contracts\SosServiceTypeRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\VehicleRepositoryInterface;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\Contracts\DiagnosisCategoryRepositoryInterface;
use App\Repositories\Contracts\DiagnosisMediaRepositoryInterface;
use App\Repositories\Contracts\DiagnosisRepositoryInterface;
use App\Repositories\Contracts\DiagnosisWorkshopSuggestionRepositoryInterface;
use App\Repositories\Contracts\LeadRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\WorkshopAnalyticsRepositoryInterface;
use App\Repositories\Contracts\WorkshopImageRepositoryInterface;
use App\Repositories\Contracts\WorkshopRepositoryInterface;
use App\Repositories\Contracts\WorkshopWorkingHourRepositoryInterface;
use App\Repositories\Contracts\ServiceLedgerRepositoryInterface;
use App\Repositories\Contracts\WhatsappMessageRepositoryInterface;
use App\Repositories\Eloquent\OtpCodeRepository;
use App\Repositories\Eloquent\BookingRepository;
use App\Repositories\Eloquent\BookingStatusLogRepository;
use App\Repositories\Eloquent\CarBrandRepository;
use App\Repositories\Eloquent\CarModelRepository;
use App\Repositories\Eloquent\DiagnosisCategoryRepository;
use App\Repositories\Eloquent\DiagnosisMediaRepository;
use App\Repositories\Eloquent\DiagnosisRepository;
use App\Repositories\Eloquent\DiagnosisWorkshopSuggestionRepository;
use App\Repositories\Eloquent\EmergencyGuidanceRepository;
use App\Repositories\Eloquent\LeadRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\ReviewRepository;
use App\Repositories\Eloquent\ServiceCategoryRepository;
use App\Repositories\Eloquent\ServiceLedgerRepository;
use App\Repositories\Eloquent\ServiceRepository;
use App\Repositories\Eloquent\SosProviderRepository;
use App\Repositories\Eloquent\SosRequestLogRepository;
use App\Repositories\Eloquent\SosRequestRepository;
use App\Repositories\Eloquent\SosServiceTypeRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\VehicleRepository;
use App\Repositories\Eloquent\WorkshopAnalyticsRepository;
use App\Repositories\Eloquent\WorkshopImageRepository;
use App\Repositories\Eloquent\WorkshopRepository;
use App\Repositories\Eloquent\WorkshopWorkingHourRepository;
use App\Repositories\Eloquent\WhatsappMessageRepository;
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
        $this->app->bind(DiagnosisRepositoryInterface::class, DiagnosisRepository::class);
        $this->app->bind(DiagnosisMediaRepositoryInterface::class, DiagnosisMediaRepository::class);
        $this->app->bind(DiagnosisWorkshopSuggestionRepositoryInterface::class, DiagnosisWorkshopSuggestionRepository::class);
        $this->app->bind(DiagnosisCategoryRepositoryInterface::class, DiagnosisCategoryRepository::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
        $this->app->bind(BookingStatusLogRepositoryInterface::class, BookingStatusLogRepository::class);
        $this->app->bind(LeadRepositoryInterface::class, LeadRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);
        $this->app->bind(WhatsappMessageRepositoryInterface::class, WhatsappMessageRepository::class);
        $this->app->bind(ServiceLedgerRepositoryInterface::class, ServiceLedgerRepository::class);
        $this->app->bind(SosServiceTypeRepositoryInterface::class, SosServiceTypeRepository::class);
        $this->app->bind(SosProviderRepositoryInterface::class, SosProviderRepository::class);
        $this->app->bind(SosRequestRepositoryInterface::class, SosRequestRepository::class);
        $this->app->bind(SosRequestLogRepositoryInterface::class, SosRequestLogRepository::class);
        $this->app->bind(EmergencyGuidanceRepositoryInterface::class, EmergencyGuidanceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
    }
}
