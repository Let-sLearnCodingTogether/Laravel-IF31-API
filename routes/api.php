<?php

use App\Enum\RoleEnum;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

// Grup route untuk pengguna yang BELUM login (guest)
Route::middleware(['guest'])->group(function () {
    // Endpoint untuk registrasi pengguna baru
    Route::post('/register', [AuthenticationController::class, 'register']);

    // Endpoint untuk login pengguna
    Route::post('/login', [AuthenticationController::class, 'login']);
});

// Grup route untuk pengguna yang SUDAH login menggunakan Sanctum (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Endpoint untuk logout
    Route::post('/logout', [AuthenticationController::class, 'logout']);

    // Endpoint untuk melihat profil pengguna yang sedang login
    Route::get('/profile', [ProfileController::class, 'profile']);

    // Grup route khusus untuk ADMIN (role admin saja yang bisa akses)
    Route::middleware("ensureUserHasRole:" . RoleEnum::ADMIN->value)->group(function () {
        // Endpoint untuk melihat daftar semua pengguna
        Route::get('/users', [UserManagementController::class, 'index']);

        // Endpoint untuk mengupdate data pengguna tertentu (PUT/PATCH)
        Route::match(['PUT', 'PATCH'], '/users/{user}', [UserManagementController::class, 'update']);
    });

    // Endpoint untuk melihat daftar review pada sebuah spot
    Route::get('/spot/{spot}/reviews', [SpotController::class, 'reviews']);

    // Resource controller untuk Spot (CRUD: create, read, update, delete)
    Route::apiResource('spot', SpotController::class);

    // Resource controller untuk Review
    Route::apiResource('review', ReviewController::class)
        // Hanya mengizinkan store (buat review) & destroy (hapus review)
        ->only(['store', 'destroy'])
        // Middleware tambahan: hanya USER biasa yang bisa membuat review
        ->middlewareFor(['store'], 'ensureUserHasRole:' . RoleEnum::USER->value)
        // Middleware tambahan: hanya ADMIN yang bisa menghapus review
        ->middlewareFor(['destroy'], 'ensureUserHasRole:' . RoleEnum::ADMIN->value);
});

