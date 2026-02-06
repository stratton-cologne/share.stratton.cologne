<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\BatchAdminApiController;

Route::prefix('api/admin')->group(function () {
	Route::post('/auth/login', [AdminAuthController::class, 'login']);
	Route::post('/auth/logout', [AdminAuthController::class, 'logout']);
	Route::get('/auth/me', [AdminAuthController::class, 'me']);
	Route::get('/auth/csrf', [AdminAuthController::class, 'csrf']);

	Route::post('/auth/mfa/totp/setup', [AdminAuthController::class, 'mfaTotpSetup'])->middleware('auth');
	Route::post('/auth/mfa/totp/verify', [AdminAuthController::class, 'mfaTotpVerify'])->middleware('auth');
	Route::post('/auth/mfa/totp/disable', [AdminAuthController::class, 'mfaTotpDisable'])->middleware('auth');

	Route::post('/auth/mfa/email/send', [AdminAuthController::class, 'mfaEmailSend'])->middleware('auth');
	Route::post('/auth/mfa/email/verify', [AdminAuthController::class, 'mfaEmailVerify'])->middleware('auth');
	Route::post('/auth/mfa/email/disable', [AdminAuthController::class, 'mfaEmailDisable'])->middleware('auth');

	Route::post('/auth/mfa/login/totp', [AdminAuthController::class, 'mfaLoginTotp']);
	Route::post('/auth/mfa/login/email/send', [AdminAuthController::class, 'mfaLoginEmailSend']);
	Route::post('/auth/mfa/login/email/verify', [AdminAuthController::class, 'mfaLoginEmailVerify']);

	Route::middleware(['auth', 'admin'])->group(function () {
		Route::get('/users', [AdminUserController::class, 'index']);
		Route::post('/users', [AdminUserController::class, 'store']);
		Route::put('/users/{user}', [AdminUserController::class, 'update']);
		Route::delete('/users/{user}', [AdminUserController::class, 'destroy']);

		Route::get('/batches', [BatchAdminApiController::class, 'index']);
		Route::post('/batches/{batch}/extend', [BatchAdminApiController::class, 'extend']);
		Route::delete('/batches/{batch}', [BatchAdminApiController::class, 'destroy']);
	});
});

Route::view('/', 'app');
Route::view('/{any}', 'app')->where('any', '.*');
