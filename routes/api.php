<?php
use App\Http\Controllers\Api\users\LoginController;
use App\Http\Controllers\Api\users\RegisterController;
use App\Http\Controllers\Api\users\ForgotpasswordController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/forgot-password', [ForgotpasswordController::class, 'forgotPassword']);
Route::post('/verify-token', [ForgotpasswordController::class, 'verifyToken']);
Route::post('/reset-password', [ForgotpasswordController::class, 'resetPassword']);




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/change-password', [ForgotpasswordController::class, 'changePassword']);
});

