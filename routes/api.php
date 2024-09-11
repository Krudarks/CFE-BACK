<?php

use App\Http\AttendanceController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfilePictureController;
use App\Http\Controllers\VehicleController;
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


/***********************************************************************************************************************
 *  APIS FREE
 **********************************************************************************************************************/

Route::post('/login', [AuthenticationController::class, 'store']);
Route::post('/register', [UserController::class, 'storeUserStudent']);

Route::get('/users/getProfilePicture/{id}/{type?}', [UserProfilePictureController::class, 'get']);

// Password reset link request routes...
Route::get('password/email', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.email');
Route::post('forgotPassword/sendEmailLink', [ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset routes...
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.request');
Route::post('forgotPassword/reset', [ResetPasswordController::class, 'reset'])->name('password.reset');

/***********************************************************************************************************************
 *  APIS WITH MIDDLEWARE
 *
 **********************************************************************************************************************/

Route::group(['middleware' => 'auth:api'], function () {
    //  Auth
    Route::prefix('auth')->group(function () {
        Route::get('/logout', [AuthenticationController::class, 'destroy']);
    });

    Route::prefix('notes')->group(function () {
        Route::get('/', [NotesController::class, 'index']);
        Route::get('/{id}', [NotesController::class, 'show']);
        Route::post('/', [NotesController::class, 'store']);
        Route::put('/{id}', [NotesController::class, 'update']);
        Route::delete('/{id}', [NotesController::class, 'delete']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/lasted', [UserController::class, 'lasted']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'delete']);

        // Password
        Route::post('/check-password', [UserController::class, 'checkPassword']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });

    Route::prefix('vehicle')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/lasted', [VehicleController::class, 'lasted']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'delete']);
    });

    // attendance/attendance/index
    Route::prefix('attendance')->group(function () {
        Route::get('attendance/index', [AttendanceController::class, 'index']);
        Route::get('attendance/show/{id}', [AttendanceController::class, 'show']);
        Route::post('attendance/register', [AttendanceController::class, 'registerEntry']);
        Route::put('attendance/update/{id}', [AttendanceController::class, 'update']);
        Route::delete('attendance/delete/{id}', [AttendanceController::class, 'softDelete']);
    });
});
