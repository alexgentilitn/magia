<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteApiController;

/*
|--------------------------------------------------------------------------
| API Routes MA.GIA DONNA
|--------------------------------------------------------------------------
|
| API REST per app mobile e integrazioni esterne
| Autenticazione: Laravel Sanctum (Bearer Token)
|
*/

// ============================================
// API PUBBLICHE (senza autenticazione)
// ============================================

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'MA.GIA DONNA API',
        'version' => '1.0',
        'timestamp' => now()->toIso8601String()
    ]);
});

// Login cliente (genera token)
Route::post('/cliente/login', [ClienteApiController::class, 'login']);

// ============================================
// API PROTETTE (richiedono autenticazione)
// ============================================

Route::middleware('auth:sanctum')->group(function () {

    // Info utente autenticato
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });

    // Logout (revoca token)
    Route::post('/cliente/logout', [ClienteApiController::class, 'logout']);

    // ============================================
    // PROFILO CLIENTE
    // ============================================
    Route::prefix('cliente')->group(function () {
        // Profilo
        Route::get('/profilo', [ClienteApiController::class, 'profilo']);
        Route::put('/profilo', [ClienteApiController::class, 'aggiornaProfilo']);

        // Lezioni
        Route::get('/lezioni', [ClienteApiController::class, 'lezioni']);
        Route::post('/lezioni/{id}/prenota', [ClienteApiController::class, 'prenotaLezione']);

        // Prenotazioni
        Route::get('/prenotazioni', [ClienteApiController::class, 'miePrenotazioni']);
        Route::delete('/prenotazioni/{id}', [ClienteApiController::class, 'annullaPrenotazione']);
    });
});
