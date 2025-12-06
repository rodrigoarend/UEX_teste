<?php
/*
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ViaCepController;

// Rotas públicas (registro, login, recuperação de senha, etc.)
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// (recuperação de senha usaría os endpoints padrão de password reset ou um controller separado)

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    // Recuperação de senha
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword']);
   
    // ajuda de endereço
    Route::get('/address/search', [AddressController::class, 'search']);
    Route::get('/address/geocode', [AddressController::class, 'geocode']);
    // CRUD de contatos – base para o restante do teste
    Route::apiResource('contacts', ContactController::class);
    // exclusão de conta (pedindo senha)
    Route::delete('/account', [AuthController::class, 'destroy']);
    // ViaCep proxy (frontend não chama direto o ViaCep)
    Route::get('/via-cep', [ViaCepController::class, 'search']);    
});
    // Rotas protegidas por Sanctum
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/user', [AuthController::class, 'destroy']);
    });
    */


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
        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
        // via cep proxy
        Route::get('/via-cep', [ViaCepController::class, 'search']);
    });
    
