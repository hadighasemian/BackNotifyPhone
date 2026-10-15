<?php

use App\Http\Controllers\Api\V1\Admin\AppVersionAdminController;
use App\Http\Controllers\Api\V1\Admin\DeviceAdminController;
use App\Http\Controllers\Api\V1\Admin\ErrorAdminController;
use App\Http\Controllers\Api\V1\Admin\NotificationAdminController;
use App\Http\Controllers\Api\V1\Admin\StatsController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\AppVersionPublicController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\ErrorController;
use App\Http\Controllers\Api\V1\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public app distribution
    Route::get('app/latest', [AppVersionPublicController::class, 'latest']);
    Route::get('app/download', [AppVersionPublicController::class, 'download']);
    Route::get('app/download/{code}', [AppVersionPublicController::class, 'download'])->whereNumber('code');

    // Device registration (public)
    Route::post('devices/register', [DeviceController::class, 'register']);

    // Device-authenticated
    Route::middleware(['auth:sanctum', 'device'])->group(function () {
        Route::post('devices/heartbeat', [DeviceController::class, 'heartbeat']);
        Route::post('notifications', [NotificationController::class, 'store']);
        Route::post('errors', [ErrorController::class, 'store']);
    });

    // Admin auth
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        Route::apiResource('admin/users', UserController::class);

        Route::get('admin/devices/map', [DeviceAdminController::class, 'map']);
        Route::get('admin/devices', [DeviceAdminController::class, 'index']);
        Route::get('admin/devices/{device}', [DeviceAdminController::class, 'show']);
        Route::get('admin/devices/{device}/states', [DeviceAdminController::class, 'states']);
        Route::patch('admin/devices/{device}', [DeviceAdminController::class, 'update']);
        Route::delete('admin/devices/{device}', [DeviceAdminController::class, 'destroy']);

        Route::get('admin/notifications', [NotificationAdminController::class, 'index']);
        Route::get('admin/notifications/{notification}', [NotificationAdminController::class, 'show']);

        Route::get('admin/errors', [ErrorAdminController::class, 'index']);
        Route::get('admin/errors/{error}', [ErrorAdminController::class, 'show']);

        Route::get('admin/app-versions', [AppVersionAdminController::class, 'index']);
        Route::post('admin/app-versions', [AppVersionAdminController::class, 'store']);
        Route::get('admin/app-versions/{app_version}', [AppVersionAdminController::class, 'show']);
        Route::patch('admin/app-versions/{app_version}', [AppVersionAdminController::class, 'update']);
        Route::post('admin/app-versions/{app_version}/activate', [AppVersionAdminController::class, 'activate']);
        Route::delete('admin/app-versions/{app_version}', [AppVersionAdminController::class, 'destroy']);

        Route::get('admin/stats', StatsController::class);
    });
});
