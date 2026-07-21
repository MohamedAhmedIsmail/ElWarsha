<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CarBrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiagnosisController as AdminDiagnosisController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Admin\WorkshopVerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('admin.dashboard'))->name('home');
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('car-brands', CarBrandController::class)->except(['show'])->parameters(['car-brands' => 'brand']);
        Route::resource('car-models', CarModelController::class)->except(['show'])->parameters(['car-models' => 'model']);
        Route::resource('service-categories', ServiceCategoryController::class)->except(['show'])->parameters(['service-categories' => 'category']);
        Route::resource('services', ServiceController::class)->except(['show']);

        Route::resource('workshops', WorkshopController::class);
        Route::post('workshops/{workshop}/approve', [WorkshopController::class, 'approve'])->name('workshops.approve');
        Route::post('workshops/{workshop}/reject', [WorkshopController::class, 'reject'])->name('workshops.reject');
        Route::post('workshops/{workshop}/suspend', [WorkshopController::class, 'suspend'])->name('workshops.suspend');
        Route::post('workshops/{workshop}/verify', [WorkshopController::class, 'verify'])->name('workshops.verify');
        Route::post('workshops/{workshop}/unverify', [WorkshopController::class, 'unverify'])->name('workshops.unverify');
        Route::post('workshops/{workshop}/images', [WorkshopController::class, 'uploadImage'])->name('workshops.images.store');
        Route::delete('workshop-images/{image}', [WorkshopController::class, 'deleteImage'])->name('workshop-images.destroy');

        Route::get('workshop-verifications', [WorkshopVerificationController::class, 'index'])->name('workshop-verifications.index');
        Route::get('workshop-verifications/{verification}', [WorkshopVerificationController::class, 'show'])->name('workshop-verifications.show');
        Route::post('workshop-verifications/{verification}/approve', [WorkshopVerificationController::class, 'approve'])->name('workshop-verifications.approve');
        Route::post('workshop-verifications/{verification}/reject', [WorkshopVerificationController::class, 'reject'])->name('workshop-verifications.reject');

        Route::get('diagnoses', [AdminDiagnosisController::class, 'index'])->name('diagnoses.index');
        Route::get('diagnoses/{diagnosis}', [AdminDiagnosisController::class, 'show'])->name('diagnoses.show');
        Route::post('diagnoses/{diagnosis}/manual-review', [AdminDiagnosisController::class, 'manualReview'])->name('diagnoses.manual-review');
        Route::post('diagnoses/{diagnosis}/complete', [AdminDiagnosisController::class, 'complete'])->name('diagnoses.complete');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::post('bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('bookings/{booking}/complete', [AdminBookingController::class, 'complete'])->name('bookings.complete');
    });
});
