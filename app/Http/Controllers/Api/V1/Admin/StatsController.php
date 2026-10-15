<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppError;
use App\Models\AppVersion;
use App\Models\Device;
use App\Models\Notification;
use App\Models\User;

class StatsController extends Controller
{
    public function __invoke()
    {
        $online = Device::online()->count();
        $totalDevices = Device::count();
        return response()->json(['data' => [
            'devices_total' => $totalDevices,
            'devices_online' => $online,
            'devices_offline' => max(0, $totalDevices - $online),
            'notifications_total' => Notification::count(),
            'notifications_today' => Notification::whereDate('received_at', today())->count(),
            'errors_total' => AppError::count(),
            'errors_today' => AppError::whereDate('created_at', today())->count(),
            'users_total' => User::count(),
            'app_versions_total' => AppVersion::count(),
            'active_app_version' => ($v = AppVersion::latestActive()) ? [
                'version_name' => $v->version_name,
                'version_code' => $v->version_code,
                'downloads' => $v->downloads,
            ] : null,
        ]]);
    }
}
