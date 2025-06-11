<?php

use App\Http\Controllers\API\ArtykulController;
use App\Http\Controllers\API\DokumentHandlowyController;
use App\Http\Controllers\API\FirmaController;
use App\Http\Controllers\API\OdbiorcaController;
use App\Http\Controllers\API\StatsController;
use App\Http\Controllers\API\AuthController;
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
    Route::get('/dokumenty', [DokumentHandlowyController::class, 'index']);
    Route::post('/dokumenty', [DokumentHandlowyController::class, 'store']);
    Route::get('/dokumenty/{id}', [DokumentHandlowyController::class, 'show']);
    Route::put('/dokumenty/{id}', [DokumentHandlowyController::class, 'update']);
    Route::delete('/dokumenty/{id}', [DokumentHandlowyController::class, 'destroy']);
    Route::post('/dokumenty/{id}/pdf', [DokumentHandlowyController::class, 'generatePdf']);
    Route::post('/dokumenty/{id}/email', [DokumentHandlowyController::class, 'sendEmail']);

    // Firmy
    Route::get('/firmy', [FirmaController::class, 'index']);
    Route::post('/firmy', [FirmaController::class, 'store']);
    Route::get('/firmy/{id}', [FirmaController::class, 'show']);
    Route::put('/firmy/{id}', [FirmaController::class, 'update']);
    Route::delete('/firmy/{id}', [FirmaController::class, 'destroy']);
    
    // Zarządzanie klientami (tylko dla firm księgowych)
    Route::middleware('check.permissions:ksiegowy,admin')->group(function () {
        Route::get('/firmy/klienci', [FirmaController::class, 'klienci']);
        Route::post('/firmy/klienci', [FirmaController::class, 'dodajKlienta']);
    });

    // Odbiorcy
    Route::get('/odbiorcy', [OdbiorcaController::class, 'index']);
    Route::post('/odbiorcy', [OdbiorcaController::class, 'store']);
    Route::get('/odbiorcy/{id}', [OdbiorcaController::class, 'show']);
    Route::put('/odbiorcy/{id}', [OdbiorcaController::class, 'update']);
    Route::delete('/odbiorcy/{id}', [OdbiorcaController::class, 'destroy']);

    // Artykuły
    Route::get('/artykuly', [ArtykulController::class, 'index']);
    Route::post('/artykuly', [ArtykulController::class, 'store']);
    Route::get('/artykuly/{id}', [ArtykulController::class, 'show']);
    Route::put('/artykuly/{id}', [ArtykulController::class, 'update']);
    Route::delete('/artykuly/{id}', [ArtykulController::class, 'destroy']);

    // Statystyki
    Route::get('/stats', [StatsController::class, 'index']);
});