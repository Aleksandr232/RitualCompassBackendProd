<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostRitualController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SendNotificationController;
use App\Http\Controllers\Api\StatisticController;
use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\PostCemeteryController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth', [AuthController::class, 'redirectToAuth']);
Route::get('/auth/callback', [AuthController::class, 'handleAuthCallback']);

Route::middleware('auth:sanctum')->group(function(){
        Route::middleware('admin')->group(function(){
            //dashboard
            Route::get('/user', [AuthController::class, 'user']);
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/rituals', [PostRitualController::class, 'post_ritual']);
            Route::get('/all/rituals', [PostRitualController::class, 'get_ritual']);
            Route::delete('/rituals/delete/{id}', [PostRitualController::class, 'delete_ritual']);
            Route::post('/rituals/{id}', [PostRitualController::class, 'update_ritual']);
            Route::get('/clients/count', [StatisticController::class, 'getClientCountByRitualCompany']);
            Route::post('/about', [AboutController::class, 'post_about']);
            Route::get('/about', [AboutController::class, 'get_about']);
            Route::post('/cemetery', [PostCemeteryController::class, 'post_cemetery']);
            Route::get('/cemetery', [PostCemeteryController::class, 'get_cemetery']);
        });
});


//Главная страница с компаними
Route::get('/all/rituals', [PostRitualController::class, 'get_ritual']);
Route::post('/send/{id}', [SendNotificationController::class, 'sendTelegram']);
Route::post('/phone/{id}', [SendNotificationController::class, 'sendPhone']);
Route::post('/rituals', [PostRitualController::class, 'post_ritual']);
Route::post('/question', [SendNotificationController::class, 'sendTelegramQuestions']);
Route::get('/about', [AboutController::class, 'get_about']);
Route::get('/about/{slug}', [AboutController::class, 'get_about_slug']);
Route::get('/cemetery', [PostCemeteryController::class, 'get_cemetery']);
Route::middleware('api-session')->group(function () {
    Route::post('/rating/{id}', [RatingController::class, 'post_rate']);
});



