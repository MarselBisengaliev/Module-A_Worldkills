<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\GameScoreController;
use App\Http\Controllers\GameVersionController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/v1/auth/signup', [UserAuthController::class, 'register']);
Route::post('/v1/auth/signin', [UserAuthController::class, 'login']);

Route::get('/v1/games', [GameController::class, 'index']);
Route::get('/v1/games/{slug}', [GameController::class, 'show']);
Route::get('/v1/games/{slug}/{version}', [GameVersionController::class, 'show'])->whereNumber('version');
Route::get('/v1/users/{username}', [UserController::class, 'show']);

Route::get('/v1/games/{slug}/scores', [GameScoreController::class, 'index']);

// Protected routes
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::post('/v1/auth/signout', [UserAuthController::class, 'logout']);
    Route::post('/v1/games', [GameController::class , 'store']);
    Route::post('/v1/games/{slug}/upload', [GameVersionController::class, 'store']);
    Route::post('/v1/games/{slug}/scores', [GameScoreController::class, 'store']);

    Route::put('/v1/games/{slug}', [GameController::class, 'update']);
    Route::delete('/v1/games/{slug}', [GameController::class, 'destroy']);
});
