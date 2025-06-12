<?php

use App\Http\Controllers\API\ArtykulController;
use App\Http\Controllers\API\DokumentHandlowyController;
use App\Http\Controllers\API\FirmaController;
use App\Http\Controllers\API\OdbiorcaController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
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

// Publiczne trasy uwierzytelniania
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Chronione trasy
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Dokumenty handlowe
    Route::apiResource('dokumenty', DokumentHandlowyController::class);
    Route::post('/dokumenty/{id}/pdf', [DokumentHandlowyController::class, 'generatePdf']);
    Route::post('/dokumenty/{id}/email', [DokumentHandlowyController::class, 'sendEmail']);

    // Firmy
    Route::apiResource('firmy', FirmaController::class);
    
    // Zarządzanie klientami (tylko dla firm księgowych)
    Route::middleware('check.permissions:ksiegowy,admin')->group(function () {
        Route::get('/firmy/klienci', [FirmaController::class, 'klienci']);
        Route::post('/firmy/klienci', [FirmaController::class, 'dodajKlienta']);
    });

    // Odbiorcy
    Route::apiResource('odbiorcy', OdbiorcaController::class);

    // Artykuły
    Route::apiResource('artykuly', ArtykulController::class);

    // Statystyki
    Route::get('/stats', [StatsController::class, 'index']);

    // Zarządzanie użytkownikami (tylko admin)
    Route::middleware('check.permissions:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('/users/{id}/assign-firma', [UserController::class, 'assignToFirma']);
        Route::post('/users/{id}/detach-firma', [UserController::class, 'detachFromFirma']);
        Route::post('/users/{id}/change-role', [UserController::class, 'changeRole']);
    });
});