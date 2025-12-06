<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ViaCepController;


/*
|--------------------------------------------------------------------------
| Rotas públicas
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login',    [AuthController::class, 'login'])->name('api.login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| Rotas protegidas (Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // dados do usuário autenticado
    Route::get('/me', [AuthController::class, 'me']);

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // exclusão da conta
    Route::delete('/account', [AuthController::class, 'destroy']);

    // contatos
    Route::apiResource('contacts', ContactController::class);

    // via cep proxy
    Route::get('/via-cep', [ViaCepController::class, 'search']);
});
