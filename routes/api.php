<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\DeclarationController;
use App\Http\Controllers\UserController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::post('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});

Route::get('/user', [UserController::class, 'all'])->middleware('auth:api');
Route::get('/declaration', [DeclarationController::class, 'all'])->middleware('auth:api');

Route::post('/declaration', [DeclarationController::class, 'create']);

Route::group(['middleware' => 'admin',], function ($router) {
    Route::delete('/declaration/{id}', [DeclarationController::class, 'delete']);
    // Route::get('/q', [DeclarationController::class, 'q']);
    Route::patch('/declaration/{id}', [DeclarationController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'delete']);
    Route::patch('/user/{id}', [UserController::class, 'update']);
});
