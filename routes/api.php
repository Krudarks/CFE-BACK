<?php

use App\Http\Controllers\AttendanceCarController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfilePictureController;
use App\Http\Controllers\VehicleController;
use App\Models\WorkerModel;
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

Route::post('/register', [AttendanceController::class, 'registerAttendance']);

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

        Route::get('/profileSetting/{user_id}', [UserProfilePictureController::class, 'settingProfile']);
        Route::post('/addProfilePicture', [UserProfilePictureController::class, 'add']);
        Route::delete('/deleteProfilePicture/{user_id}', [UserProfilePictureController::class, 'delete']);


        // Password
        Route::post('/check-password', [UserController::class, 'checkPassword']);
        Route::post('/change-password', [UserController::class, 'changePassword']);
    });

    Route::prefix('worker')->group(function () {
        Route::get('/', [WorkerModel::class, 'index']);
        Route::get('/{id}', [WorkerModel::class, 'show']);;
    });

    Route::prefix('vehicle')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/lasted', [VehicleController::class, 'lasted']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'delete']);
    });

    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::get('/getById/{id}', [AttendanceController::class, 'show']);

        Route::get('/report/{id}', [AttendanceController::class, 'downloadReport']);
        Route::put('/{id}', [AttendanceController::class, 'update']);
        Route::delete('/{id}', [AttendanceController::class, 'destroy']);
        Route::get('/status/{controlNumber}', [AttendanceController::class, 'getAttendanceStatus']);
        Route::post('/details', [AttendanceController::class, 'getAttendanceDetails']);
        Route::get('/all', [AttendanceController::class, 'getAllAttendances']);
    });

    Route::prefix('attenCar')->group(function () {
        Route::get('/reports', [AttendanceCarController::class, 'index']);
        Route::get('/reports/{id}', [AttendanceCarController::class, 'show']);
        Route::post('/reports', [AttendanceCarController::class, 'storeDailyReport']);
        Route::delete('/reports/{id}', [AttendanceCarController::class, 'destroy']);
        Route::get('/vehicles', [AttendanceCarController::class, 'getVehiclesForReport']);
    });
});
