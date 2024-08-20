<?php

use App\Http\Controllers\QueueController;
use App\Http\Controllers\AuthUserController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route::get('/v1/test/', [StoreController::class, 'test']);

// use App\Http\Controllers\UserController;
// Route::get('/v1/auth-users/', [AuthUserController::class, 'index']);
// Route::get('/v1/user/{id}', [AuthUserController::class, 'get_user']);
// Route::post('/v1/user-update', [AuthUserController::class, 'update_user']);


// authorization
Route::post('/v1/login/', [UsersController::class, 'login']);
Route::post('/v1/register/', [UsersController::class, 'register_valid']);
Route::post('/v1/refresh-token/', [UsersController::class, 'refresh_token']);
Route::get('/v1/user', [UsersController::class, 'info']); //user?id=
Route::post('/v1/user/info', [UsersController::class, 'update_info']);
// Route::get('/v1/user/list', [UsersController::class, 'index']);

// favorite
Route::post('/v1/user/favorite', [FavoriteController::class, 'add_favorite']);
Route::post('/v1/user/unfavorite', [FavoriteController::class, 'remove_favorite']);
Route::get('/v1/favorites', [FavoriteController::class, 'favorites']);


// store
// Route::get('/v1/store/list', [StoreController::class, 'stores']);
Route::post('/v1/store/start', [StoreController::class, 'start']);
Route::post('/v1/store/info', [StoreController::class, 'update_info']);
Route::post('/v1/store/open-close', [StoreController::class, 'open_close']);
Route::delete('/v1/store/delete', [StoreController::class, 'temporary_delete']);
Route::get('/v1/store/{id}', [StoreController::class, 'get_store_by_id']);

// products
// Route::get('/v1/product/list', [ProductController::class, 'index']);
Route::get('/v1/product/{store_id}', [ProductController::class, 'get_by_store']);
Route::post('/v1/product/add', [ProductController::class, 'add_product']);
Route::delete('/v1/product/delete', [ProductController::class, 'delete_product']);
Route::post('/v1/product/edit', [ProductController::class, 'update_product_info']);
Route::post('/v1/product/enable', [ProductController::class, 'enable_product']);

// queue
Route::post('/v1/queue/start', [QueueController::class, 'create']);
Route::post('/v1/queue/restart', [QueueController::class, 'restart']);
Route::post('/v1/queue/next-user', [QueueController::class, 'nextUser']);
Route::post('/v1/queue/enqueue', [QueueController::class, 'enqueue']);
Route::post('/v1/queue/pause', [QueueController::class, 'pause']);
Route::post('/v1/queue/close', [QueueController::class, 'close']);
Route::post('/v1/queue/update-info', [QueueController::class, 'updateInfo']);
Route::post('/v1/queue/bind-store', [QueueController::class, 'bindStore']);
Route::delete('/v1/queue', [QueueController::class, 'destroy']);

