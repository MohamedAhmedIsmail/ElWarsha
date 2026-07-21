<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CarBrandController;
use App\Http\Controllers\Admin\CarModelController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\UserController;
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
    });
});
