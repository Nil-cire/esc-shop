<?php

use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/v1/test/', [UsersController::class, 'test']);

// use App\Http\Controllers\UserController;

Route::get('/v1/auth-users/', [AuthUserController::class, 'index']);

Route::get('/v1/user/{id}', [AuthUserController::class, 'get_user']);

Route::post('/v1/user-update', [AuthUserController::class, 'update_user']);


// authorization
Route::post('/v1/login/', [UsersController::class, 'login']);
Route::post('/v1/register/', [UsersController::class, 'register_valid']);
Route::post('/v1/refresh-token/', [UsersController::class, 'refresh_token']);



