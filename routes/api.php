<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\TokenController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [RegisterController::class, 'register'])
            ->middleware([
                'throttle:register-tier1',
                'throttle:register-tier2',
                'throttle:register-tier3',
            ]);
        Route::post('login', [AuthController::class, 'login'])
            ->middleware([
                'throttle:login-tier1',
                'throttle:login-tier2',
                'throttle:login-tier3',
            ])
            ->name('login');
        Route::post('email/resend-verification', [EmailVerificationController::class, 'resendVerificationEmail']);
    });

    Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed'])
        ->name('verification.verify');

    Route::get('email/resend/{user}', [EmailVerificationController::class, 'resendWithoutAuth'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.resend_new');

    Route::middleware('jwt.auth')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/token/refresh', [TokenController::class, 'refresh']);
        Route::post('auth/email/verification-notification', [EmailVerificationController::class, 'resendVerificationEmail'])
            ->middleware('throttle:6,1')->name('verification.send');
        Route::put('profile', [ProfileController::class, 'update'])->middleware('verified');
        Route::get('users/{username}', [UserController::class, 'show'])->name('api.users.show');
    });
});
