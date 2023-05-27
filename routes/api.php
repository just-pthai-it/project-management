<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectStatusController;
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\UserController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    Route::get('profile', [UserController::class, 'myProfile']);
    Route::patch('profile', [UserController::class, 'updateMyProfile']);
    Route::post('profile/avatar', [UserController::class, 'updateMyAvatar']);

    Route::post('users/{user}/avatar', [UserController::class, 'updateAvatar']);
    Route::get('users/search', [UserController::class, 'search']);
    Route::apiResource('users', UserController::class);

    Route::apiResource('project-statuses', ProjectStatusController::class)->only(['index']);

    Route::apiResource('task-statuses', TaskStatusController::class)->only(['index']);


    Route::get('projects/{project}/history', [ProjectController::class, 'history']);
    Route::apiResource('projects.users', ProjectUserController::class)->only(['index']);
    Route::get('projects/search', [ProjectController::class, 'search']);
    Route::apiResource('projects', ProjectController::class);


    Route::get('projects/{project}/tasks/search', [ProjectTaskController::class, 'search']);
    Route::get('projects/{project}/tasks/kanban', [ProjectTaskController::class, 'indexKanban']);
    Route::apiResource('projects.tasks', ProjectTaskController::class);
    Route::post('tasks/{task}/attach-files', [TaskController::class, 'attachFiles']);
    Route::delete('tasks/{task}/detach-file/{file}', [TaskController::class, 'detachFile']);
    Route::post('tasks/{task}/report', [TaskController::class, 'submitReport']);
    Route::delete('tasks/{task}/report', [TaskController::class, 'destroyReport']);
    Route::get('tasks/{task}/history', [TaskController::class, 'history']);
    Route::get('tasks/search', [TaskController::class, 'search']);
    Route::apiResource('tasks', TaskController::class)->only(['index']);

    Route::apiResource('tasks.comments', TaskCommentController::class)->only(['index', 'store']);
    Route::get('comments/{comment}/replies', [CommentController::class, 'listReplies']);
    Route::apiResource('comments', CommentController::class)->only(['update', 'destroy']);

    Route::post('notifications/{notification}/marks-as-read', [NotificationController::class, 'marksAsRead']);
    Route::post('notifications/marks-all-as-read', [NotificationController::class, 'marksAllAsRead']);
    Route::get('notifications/count-unread', [NotificationController::class, 'countUnreadNotifications']);
    Route::apiResource('notifications', NotificationController::class)->only(['index']);

    Route::apiResource('roles', RoleController::class);

    Route::apiResource('permissions', PermissionController::class)->only(['index']);

    Route::post('upload-file', [ResourceController::class, 'upload']);
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

Route::get('/reset-password/{token}', function ($token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('/reset-password', function (Request $request) {

    return \App\Models\Notification::all();
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ]);

            $user->save();

            event(new PasswordReset($user));
        }
    );

//    return $status === Password::PASSWORD_RESET
//                ? redirect()->route('login')->with('status', __($status))
//                : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');