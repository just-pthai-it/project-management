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
    Route::get('projects/{project}/history', [ProjectController::class, 'history']);
    Route::get('projects/search', [ProjectController::class, 'search']);
    Route::apiResource('projects', ProjectController::class);


    Route::get('projects/{project}/tasks/search', [ProjectTaskController::class, 'search']);
    Route::apiResource('projects.tasks', ProjectTaskController::class);
    Route::post('tasks/{task}/attach-files', [TaskController::class, 'attachFiles']);
    Route::delete('tasks/{task}/detach-file/{file}', [TaskController::class, 'detachFile']);
    Route::post('tasks/{task}/report', [TaskController::class, 'submitReport']);
    Route::delete('tasks/{task}/report', [TaskController::class, 'destroyReport']);
    Route::get('tasks/{task}/history', [TaskController::class, 'history']);
    Route::get('tasks/search', [TaskController::class, 'search']);
    Route::apiResource('tasks', TaskController::class)->only(['index']);

    Route::apiResource('tasks.comments', TaskCommentController::class)->only(['store']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'listReplies']);
    Route::apiResource('comments', CommentController::class)->only(['update', 'destroy']);
});