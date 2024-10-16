<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Middleware\ApplicationLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([ApplicationLicense::class])->group(function () {
    Route::middleware(['auth:sanctum'])->get('/v1/user', function (Request $request) {
        return $request->user();
    });
    Route::post('v1/login', [AuthController::class, 'login']);
    Route::post('v1/register', [AuthController::class, 'register']);
    Route::post('v1/forgot', [AuthController::class, 'forgot']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('v1/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::get('v1/account/profile', [AccountController::class, 'show']);
        Route::post('v1/account/profile/update', [AccountController::class, 'update']);
        Route::post('v1/account/profile/{type}', [AccountController::class, 'switchProfile']);
        Route::get('v1/account/profile-to-switch', [AccountController::class, 'profileToSwitch']);
        Route::post('v1/account/profile-trader/verify', [AccountController::class, 'verifyProfile']);
        Route::get('v1/account/delete', [AccountController::class, 'delete']);

        Route::get('v1/cars', [VehicleController::class, 'index']);
        Route::get('v1/cars/single/{id}', [VehicleController::class, 'single']);
        Route::post('v1/cars/photo/{id}', [VehicleController::class, 'images']);

        Route::post('v1/sliders', [VehicleController::class, 'sliders']);

        // change password
        Route::post('v1/change-password', [AuthController::class, 'changePassword']);

        // logout
        Route::get('v1/logout', [AuthController::class, 'logout']);
    });

    // Vehicle
    Route::get('v1/vehicles/car', [VehicleController::class, 'indexNew']);
    Route::get('v1/vehicles/manufacturer', [VehicleController::class, 'manufacturer']);
    Route::get('v1/vehicles/model', [VehicleController::class, 'model']);
    Route::get('v1/vehicles/get-model-by-manufacturer/{manufacturer}', [VehicleController::class, 'getModelByManufacturer']);
    Route::post('v1/vehicles/search', [VehicleController::class, 'index']);
    Route::post('v1/vehicles/filter-search', [VehicleController::class, 'index']);
    Route::get('v1/vehicles/car/show/{id}', [VehicleController::class, 'single']);
    Route::get('v1/vehicles/similar-cars/{id}', [VehicleController::class, 'similarCars']);
});
