<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TagController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test', function () {
    return response()->json(['message' => 'Hello from Laravel!']);
});

Route::get('/tasks', [TaskController::class, 'index']);

Route::get('/tags', [TagController::class, 'index']);

Route::post('/tasks', [TaskController::class, 'store']);

Route::post('/tags', [TagController::class, 'store']);

Route::put('/tasks/{id}', [TaskController::class, 'update']);