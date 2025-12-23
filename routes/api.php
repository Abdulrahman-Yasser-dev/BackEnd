<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\UserInformationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WorkRequestController;

Route::get('/users', [UserInformationController::class, 'index']);
Route::get('/users/{id}', [UserInformationController::class, 'show']);
Route::post('/users', [UserInformationController::class, 'store']);
Route::put('/users/{id}', [UserInformationController::class, 'update']);
Route::delete('/users/{id}', [UserInformationController::class, 'destroy']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user/current', [AuthController::class, 'currentUser'])->middleware('auth:sanctum');





Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::put('/profile/update/{id}', [ProfileController::class, 'update']);
Route::put('/profile/update-role/{id}', [ProfileController::class, 'switchRole']);
Route::post('/profile/upload-avatar/{id}', [ProfileController::class, 'uploadAvatar']);


Route::get('/portfolio/{userId}', [PortfolioController::class, 'index']);
Route::post('/portfolio', [PortfolioController::class, 'store']);
Route::post('/profile/upsert', [ProfileController::class, 'upsert'])->middleware('auth:sanctum');
Route::delete('/portfolio/{userId}/{itemId}', [PortfolioController::class, 'destroy']);



Route::get('/work-requests', [WorkRequestController::class, 'index']);
Route::post('/work-request', [WorkRequestController::class, 'store']);
