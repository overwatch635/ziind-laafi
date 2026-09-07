<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\FavoriteController;

// ============================================================
// FICHIER NOUVEAU — routes/api.php n'existait pas avant.
// Toutes les routes ici répondent en JSON (au lieu de vues HTML).
// ============================================================

// ROUTES PUBLIQUES (pas besoin d'être connecté)
Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);

// ROUTES PROTÉGÉES (nécessitent un token Sanctum -> header
// Authorization: Bearer {token})
Route::middleware('auth:sanctum')->group(function () {

    // Biens (réservé bailleur/agent -> vérification faite DANS le controller,
    // comme dans la version web)
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

    // Favoris (réservé client)
    Route::post('/favorites/{id}', [FavoriteController::class, 'toggle']);
    Route::get('/mes-favoris', [FavoriteController::class, 'mesFavoris']);
});

// ------------------------------------------------------------
// À COMPLÉTER PAR ALAN (Auth + User + VisitRequest) :
//
// Route::post('/register', [Api\AuthController::class, 'register']);
// Route::post('/login',    [Api\AuthController::class, 'login']);
//
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [Api\AuthController::class, 'logout']);
//     Route::post('/properties/{id}/visit', [Api\VisitRequestController::class, 'store']);
//     Route::get('/mes-visites', [Api\VisitRequestController::class, 'mesVisites']);
//     ... routes agent + manager (voir répartition du groupe)
// });
// ------------------------------------------------------------
