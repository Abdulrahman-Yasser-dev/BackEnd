<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\UserInformationController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WorkRequestController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\NotificationController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/users', [UserInformationController::class, 'index']);
Route::get('/users/{id}', [UserInformationController::class, 'show']);
Route::post('/users', [UserInformationController::class, 'store']);
Route::put('/users/{id}', [UserInformationController::class, 'update']);
Route::delete('/users/{id}', [UserInformationController::class, 'destroy']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user/current', [AuthController::class, 'currentUser'])->middleware('auth:sanctum');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::put('/user/update-password/{id}', [AuthController::class, 'updatePassword']);



Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    $user = User::find($id);

    if (!$user) {
        return redirect('http://localhost:5173/verify-error');
    }

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return redirect('http://localhost:5173/verify-error');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect('http://localhost:5173/email-verified');
})->middleware('signed')->name('verification.verify');



Route::post('/email/resend', [AuthController::class, 'resendVerification']);
Route::post('/reset-password-validate', [AuthController::class, 'validateResetToken']);




Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::put('/profile/update/{id}', [ProfileController::class, 'update']);
Route::put('/profile/update-role/{id}', [ProfileController::class, 'switchRole']);
Route::post('/profile/upload-avatar/{id}', [ProfileController::class, 'uploadAvatar']);


Route::get('/portfolio/{userId}', [PortfolioController::class, 'index']);
Route::post('/portfolio', [PortfolioController::class, 'store']);
Route::post('/portfolio/upload-files', [PortfolioController::class, 'uploadFiles']);
Route::post('/profile/upsert', [ProfileController::class, 'upsert'])->middleware('auth:sanctum');
Route::delete('/portfolio/{userId}/{itemId}', [PortfolioController::class, 'destroy']);



Route::get('/work-requests', [WorkRequestController::class, 'index']);
Route::post('/work-request', [WorkRequestController::class, 'store']);
Route::put('/work-request/{id}/status', [WorkRequestController::class, 'updateStatus'])->middleware('auth:sanctum');
Route::put('/work-request/{id}/assign-provider', [WorkRequestController::class, 'assignProvider'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chat/conversations', [ChatController::class, 'getConversations']);
    Route::get('/chat/{workRequestId}', [ChatController::class, 'getMessages']);
    Route::post('/chat', [ChatController::class, 'sendMessage']);

    // My Requests & Reviews
    Route::get('/work-requests/my', [WorkRequestController::class, 'myRequests']);
    Route::post('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [\App\Http\Controllers\Api\ReviewController::class, 'update']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});

Route::get('/reviews/user/{userId}', [\App\Http\Controllers\Api\ReviewController::class, 'getReviewsForUser']);
