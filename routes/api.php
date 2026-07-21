<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\EmergencyGuidanceController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\MaintenanceItemController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProviderSosRequestController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceLedgerController;
use App\Http\Controllers\SosRequestController;
use App\Http\Controllers\SosServiceTypeController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleMaintenanceReminderController;
use App\Http\Controllers\WorkshopCrmController;
use App\Http\Controllers\WorkshopDirectoryController;
use App\Http\Controllers\WorkshopTrackingController;
use App\Http\Controllers\WorkshopBookingController;
use App\Http\Controllers\WorkshopOwnerController;
use App\Http\Controllers\WorkshopSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function (): void {
    Route::post('request-otp', [AuthController::class, 'requestOtp']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
    Route::put('me', [AuthController::class, 'updateMe']);
    Route::apiResource('vehicles', VehicleController::class)->whereNumber('vehicle');
    Route::apiResource('diagnoses', DiagnosisController::class)->only(['index', 'store', 'show'])->whereNumber('diagnosis');
    Route::post('diagnoses/{diagnosis}/media', [DiagnosisController::class, 'uploadMedia'])->whereNumber('diagnosis');
    Route::get('diagnoses/{diagnosis}/recommended-workshops', [DiagnosisController::class, 'recommendedWorkshops'])->whereNumber('diagnosis');
    Route::apiResource('bookings', BookingController::class)->only(['index', 'store', 'show'])->whereNumber('booking');
    Route::put('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->whereNumber('booking');
    Route::get('sos-requests', [SosRequestController::class, 'index']);
    Route::post('sos-requests', [SosRequestController::class, 'store']);
    Route::get('sos-requests/{sosRequest}', [SosRequestController::class, 'show'])->whereNumber('sosRequest');
    Route::put('sos-requests/{sosRequest}/cancel', [SosRequestController::class, 'cancel'])->whereNumber('sosRequest');
    Route::post('emergency-guidance', [EmergencyGuidanceController::class, 'store']);
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::get('my-reviews', [ReviewController::class, 'mine']);
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->whereNumber('review');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->whereNumber('review');
    Route::get('vehicles/{vehicle}/maintenance-reminders', [VehicleMaintenanceReminderController::class, 'index'])->whereNumber('vehicle');
    Route::post('vehicles/{vehicle}/maintenance-reminders', [VehicleMaintenanceReminderController::class, 'store'])->whereNumber('vehicle');
    Route::get('vehicles/{vehicle}/service-ledger', [ServiceLedgerController::class, 'index'])->whereNumber('vehicle');
    Route::post('vehicles/{vehicle}/service-ledger', [ServiceLedgerController::class, 'store'])->whereNumber('vehicle');
    Route::get('maintenance-reminders/upcoming', [VehicleMaintenanceReminderController::class, 'upcoming']);
    Route::put('maintenance-reminders/{reminder}', [VehicleMaintenanceReminderController::class, 'update'])->whereNumber('reminder');
    Route::delete('maintenance-reminders/{reminder}', [VehicleMaintenanceReminderController::class, 'destroy'])->whereNumber('reminder');
    Route::get('service-ledger/{ledger}', [ServiceLedgerController::class, 'show'])->whereNumber('ledger');
    Route::put('service-ledger/{ledger}', [ServiceLedgerController::class, 'update'])->whereNumber('ledger');
    Route::delete('service-ledger/{ledger}', [ServiceLedgerController::class, 'destroy'])->whereNumber('ledger');
    Route::post('service-ledger/{ledger}/media', [ServiceLedgerController::class, 'uploadMedia'])->whereNumber('ledger');

    Route::prefix('workshop')->group(function (): void {
        Route::post('register', [WorkshopOwnerController::class, 'register']);
        Route::get('profile', [WorkshopOwnerController::class, 'profile']);
        Route::put('profile', [WorkshopOwnerController::class, 'updateProfile']);
        Route::post('images', [WorkshopOwnerController::class, 'uploadImages']);
        Route::delete('images/{image}', [WorkshopOwnerController::class, 'deleteImage'])->whereNumber('image');
        Route::put('services', [WorkshopOwnerController::class, 'syncServices']);
        Route::put('brands', [WorkshopOwnerController::class, 'syncBrands']);
        Route::put('working-hours', [WorkshopOwnerController::class, 'syncWorkingHours']);

        Route::get('bookings', [WorkshopBookingController::class, 'index']);
        Route::get('bookings/{booking}', [WorkshopBookingController::class, 'show'])->whereNumber('booking');
        Route::put('bookings/{booking}/accept', [WorkshopBookingController::class, 'accept'])->whereNumber('booking');
        Route::put('bookings/{booking}/decline', [WorkshopBookingController::class, 'decline'])->whereNumber('booking');
        Route::put('bookings/{booking}/start', [WorkshopBookingController::class, 'start'])->whereNumber('booking');
        Route::put('bookings/{booking}/complete', [WorkshopBookingController::class, 'complete'])->whereNumber('booking');

        Route::get('leads', [WorkshopCrmController::class, 'index']);
        Route::get('leads/{lead}', [WorkshopCrmController::class, 'show'])->whereNumber('lead');
        Route::put('leads/{lead}/status', [WorkshopCrmController::class, 'updateStatus'])->whereNumber('lead');
        Route::post('leads/{lead}/notes', [WorkshopCrmController::class, 'addNote'])->whereNumber('lead');
        Route::get('crm/analytics', [WorkshopCrmController::class, 'analytics']);
        Route::get('subscription', [WorkshopSubscriptionController::class, 'show']);
        Route::post('subscription/request', [WorkshopSubscriptionController::class, 'request']);
    });

    Route::prefix('provider')->group(function (): void {
        Route::get('sos-requests', [ProviderSosRequestController::class, 'index']);
        Route::get('sos-requests/{sosRequest}', [ProviderSosRequestController::class, 'show'])->whereNumber('sosRequest');
        Route::put('sos-requests/{sosRequest}/accept', [ProviderSosRequestController::class, 'accept'])->whereNumber('sosRequest');
        Route::put('sos-requests/{sosRequest}/decline', [ProviderSosRequestController::class, 'decline'])->whereNumber('sosRequest');
        Route::put('sos-requests/{sosRequest}/on-the-way', [ProviderSosRequestController::class, 'onTheWay'])->whereNumber('sosRequest');
        Route::put('sos-requests/{sosRequest}/arrived', [ProviderSosRequestController::class, 'arrived'])->whereNumber('sosRequest');
        Route::put('sos-requests/{sosRequest}/complete', [ProviderSosRequestController::class, 'complete'])->whereNumber('sosRequest');
    });
});

Route::get('car-brands', [LookupController::class, 'carBrands']);
Route::get('car-brands/{brand}/models', [LookupController::class, 'carBrandModels'])->whereNumber('brand');
Route::get('car-models', [LookupController::class, 'carModels']);

Route::get('service-categories', [LookupController::class, 'serviceCategories']);
Route::get('service-categories/{category}/services', [LookupController::class, 'serviceCategoryServices'])->whereNumber('category');
Route::get('services', [LookupController::class, 'services']);
Route::get('plans', [PlanController::class, 'index']);
Route::get('sos-service-types', [SosServiceTypeController::class, 'index']);
Route::get('maintenance-items', [MaintenanceItemController::class, 'index']);

Route::get('workshops', [WorkshopDirectoryController::class, 'index']);
Route::get('workshops/nearby', [WorkshopDirectoryController::class, 'nearby']);
Route::get('workshops/{workshop}', [WorkshopDirectoryController::class, 'show'])->whereNumber('workshop');
Route::get('workshops/{workshop}/services', [WorkshopDirectoryController::class, 'services'])->whereNumber('workshop');
Route::get('workshops/{workshop}/reviews', [WorkshopDirectoryController::class, 'reviews'])->whereNumber('workshop');
Route::post('workshops/{workshop}/track-view', [WorkshopTrackingController::class, 'view'])->whereNumber('workshop');
Route::post('workshops/{workshop}/track-call', [WorkshopTrackingController::class, 'call'])->whereNumber('workshop');
Route::post('workshops/{workshop}/track-whatsapp', [WorkshopTrackingController::class, 'whatsapp'])->whereNumber('workshop');
Route::post('workshops/{workshop}/track-directions', [WorkshopTrackingController::class, 'directions'])->whereNumber('workshop');
