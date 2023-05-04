<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('login', [AuthenticationController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function ()
{
    Route::post('refresh-token', [AuthenticationController::class, 'refreshToken']);

    Route::get('me', [UserController::class, 'me']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('projects.tasks', ProjectTaskController::class);

    Route::post('tasks/{task}/attach-files', [TaskController::class, 'attachFiles']);
    Route::delete('tasks/{task}/detach-file/{file}', [TaskController::class, 'detachFile']);
    Route::post('tasks/{task}/submit-report', [TaskController::class, 'submitReport']);
    Route::delete('tasks/{task}/delete-report', [TaskController::class, 'destroyReport']);
    Route::apiResource('tasks', TaskController::class)->only(['index']);
    Route::apiResource('tasks.comments', TaskCommentController::class)->only(['store']);
    Route::apiResource('comments', CommentController::class)->only(['update', 'destroy']);
});