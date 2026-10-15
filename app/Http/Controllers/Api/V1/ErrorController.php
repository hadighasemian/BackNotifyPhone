<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreErrorRequest;
use App\Http\Resources\AppErrorResource;
use App\Models\AppError;
use App\Models\Device;
use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public function store(StoreErrorRequest $request)
    {
        /** @var Device $device */
        $device = $request->user();
        $v = $request->validated();
        $err = AppError::create([
            'device_id' => $device->id,
            'level' => $v['level'] ?? 'error',
            'message' => $v['message'],
            'stacktrace' => $v['stacktrace'] ?? null,
            'app_version' => $v['app_version'] ?? $device->app_version,
            'os_version' => $v['os_version'] ?? $device->os_version,
            'context' => $v['context'] ?? null,
        ]);
        $device->forceFill(['last_seen_at' => now()])->save();
        return response()->json(['data' => new AppErrorResource($err)], 201);
    }
}
