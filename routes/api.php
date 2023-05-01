<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectTaskController;
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

});


Route::get('test', function ()
{
  $a           = [];
        $a['tasks as testtt'] = function ($query)
        {

            $query->where('status_id', '=', 1);
        };

        dump($a);
        dump([
                 'tasks as test' => function ($query)
                 {
                     $query->where('status_id', '=', 1);
                 },
             ]);
});