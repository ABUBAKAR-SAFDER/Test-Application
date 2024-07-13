<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentModerationController;
use App\Http\Controllers\HarmfulWordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Models\HarmfulWord;
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

// Route::get('/create-admin', [AuthController::class, 'createAdmin']);

Route::middleware(['throttle:api'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'role:Super Admin|User'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'role:Super Admin'])->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('list-users', [UserController::class, 'listUsers']);
        Route::post('create-or-update-user', [UserController::class, 'createOrUpdateUser']);
        Route::post('delete-user', [UserController::class, 'deleteUser']);
    });

    Route::prefix('harmfull-word')->group(function () {
        Route::get('list-words', [HarmfulWordController::class, 'listWords']);
        Route::post('create-or-update-word', [HarmfulWordController::class, 'createOrUpdateWord']);
        Route::post('delete-word', [HarmfulWordController::class, 'deleteWord']);
    });

});

Route::middleware(['auth:sanctum', 'role:Super Admin|User'])->group(function () {
    Route::prefix('post')->group(function () {
        Route::get('list-posts', [PostController::class, 'listPosts']);
        Route::post('create-or-update-post', [PostController::class, 'createOrUpdatePost']);
        Route::post('delete-post', [PostController::class, 'deletePost']);
        Route::post('report-post', [PostController::class, 'reportPost']);
    });
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
