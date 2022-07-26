<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
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

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::group(["middleware" => "jwt.auth"] , function() {
    Route::get('/myProfile', [AuthController::class, 'myProfile']);
    Route::post('/logout', [AuthController::class, 'logout']); 
});

Route::group(["middleware" => "jwt.auth"] , function() {
    Route::post('/tasks', [TaskController::class, 'createTask']);
    Route::get('/myTasks', [TaskController::class, 'myTasks']);
    Route::get('/myTask/{id}', [TaskController::class, 'getTaskById']);
    Route::delete('/deleteMyTask/{id}', [TaskController::class, 'deleteMyTask']);
    Route::put('/updateMyTask/{id}', [TaskController::class, 'updateMyTask']);
    Route::get('/user/task/{id}', [TaskController::class, 'getUserByTaskId']);
});

Route::group(["middleware" => ["jwt.auth", "isSuperAdmin"]] , function() {
    Route::post('/user/super_admin/{id}', [UserController::class, 'promoteUserToSuperAdmin']);
    Route::post('/user/remove_super_admin/{id}', [UserController::class, 'degradeUserFromSuperAdmin']);
});
