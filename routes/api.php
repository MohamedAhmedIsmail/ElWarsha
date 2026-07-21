<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkshopDirectoryController;
use App\Http\Controllers\WorkshopOwnerController;
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

    Route::prefix('workshop')->group(function (): void {
        Route::post('register', [WorkshopOwnerController::class, 'register']);
        Route::get('profile', [WorkshopOwnerController::class, 'profile']);
        Route::put('profile', [WorkshopOwnerController::class, 'updateProfile']);
        Route::post('images', [WorkshopOwnerController::class, 'uploadImages']);
        Route::delete('images/{image}', [WorkshopOwnerController::class, 'deleteImage'])->whereNumber('image');
        Route::put('services', [WorkshopOwnerController::class, 'syncServices']);
        Route::put('brands', [WorkshopOwnerController::class, 'syncBrands']);
        Route::put('working-hours', [WorkshopOwnerController::class, 'syncWorkingHours']);
    });
});

Route::get('car-brands', [LookupController::class, 'carBrands']);
Route::get('car-brands/{brand}/models', [LookupController::class, 'carBrandModels'])->whereNumber('brand');
Route::get('car-models', [LookupController::class, 'carModels']);

Route::get('service-categories', [LookupController::class, 'serviceCategories']);
Route::get('service-categories/{category}/services', [LookupController::class, 'serviceCategoryServices'])->whereNumber('category');
Route::get('services', [LookupController::class, 'services']);

Route::get('workshops', [WorkshopDirectoryController::class, 'index']);
Route::get('workshops/nearby', [WorkshopDirectoryController::class, 'nearby']);
Route::get('workshops/{workshop}', [WorkshopDirectoryController::class, 'show'])->whereNumber('workshop');
Route::get('workshops/{workshop}/services', [WorkshopDirectoryController::class, 'services'])->whereNumber('workshop');
Route::get('workshops/{workshop}/reviews', [WorkshopDirectoryController::class, 'reviews'])->whereNumber('workshop');
