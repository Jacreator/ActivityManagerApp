<?php

use App\Http\Controllers\ActivityController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

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

Route::post('/v1/login', [AuthController::class, 'signIn']);
Route::post('/v1/register', [AuthController::class, 'signUp']);

Route::prefix('v1')->middleware('auth:sanctum')->group(
    function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/activity/overtime', [ActivityController::class, 'OverTime']);
        Route::post('/activity', [ActivityController::class, 'store'])
            ->middleware('isAdmin');
        Route::put('/activity/{activity}', [ActivityController::class, 'update']);
        Route::delete('/activity/{activity}', [ActivityController::class, 'destroy']);
    }
);