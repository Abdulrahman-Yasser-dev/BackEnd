<?php

use App\Http\Controllers\UserInformationController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UserInformationController::class, 'index']);
Route::get('/users/{id}', [UserInformationController::class, 'show']);
Route::post('/users', [UserInformationController::class, 'store']);
Route::put('/users/{id}', [UserInformationController::class, 'update']);
Route::delete('/users/{id}', [UserInformationController::class, 'destroy']);
