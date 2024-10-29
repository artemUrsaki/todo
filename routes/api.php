<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/users', [UserController::class, 'view']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/tasks/own', [TaskController::class, 'indexOwn']);
    Route::get('/tasks/shared', [TaskController::class, 'indexShared']);
    Route::apiResource('/tasks', TaskController::class);

    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore']);

    Route::post('/tasks/{task}/share', [TaskController::class, 'share']);
    Route::post('/tasks/{task}/unshare', [TaskController::class, 'unShare']);

    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete']);
    
    Route::post('/tasks/{task}/setCategory', [TaskController::class, 'setCategory']);
    Route::post('/tasks/{task}/unsetCategory', [TaskController::class, 'unsetCategory']);
});
