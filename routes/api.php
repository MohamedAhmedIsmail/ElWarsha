<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LookupController;
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
});

Route::get('car-brands', [LookupController::class, 'carBrands']);
Route::get('car-brands/{brand}/models', [LookupController::class, 'carBrandModels'])->whereNumber('brand');
Route::get('car-models', [LookupController::class, 'carModels']);

Route::get('service-categories', [LookupController::class, 'serviceCategories']);
Route::get('service-categories/{category}/services', [LookupController::class, 'serviceCategoryServices'])->whereNumber('category');
Route::get('services', [LookupController::class, 'services']);
