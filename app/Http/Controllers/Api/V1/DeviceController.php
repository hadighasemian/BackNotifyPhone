<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\HeartbeatRequest;
use App\Http\Requests\RegisterDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(RegisterDeviceRequest $request)
    {
        $data = $request->validated();
        $device = Device::firstOrNew(['device_uuid' => $data['device_uuid']]);
        $device->fill(collect($data)->except('device_uuid')->all());
        $device->notification_access = (bool) ($data['notification_access'] ?? $device->notification_access ?? false);
        $device->battery_optimization_ignored = (bool) ($data['battery_optimization_ignored'] ?? $device->battery_optimization_ignored ?? false);
        $device->last_seen_at = now();
        $device->save();

        $device->tokens()->delete();
        $token = $device->createToken('device')->plainTextToken;
        $device->token = hash('sha256', explode('|', $token, 2)[1] ?? $token);
        $device->save();

        return response()->json(['data' => [
            'device_token' => $token,
            'device_id' => $device->id,
            'device' => new DeviceResource($device),
        ]], $device->wasRecentlyCreated ? 201 : 200);
    }

    public function heartbeat(HeartbeatRequest $request)
    {
        /** @var Device $device */
        $device = $request->user();
        $v = $request->validated();

        $device->fill(array_filter([
            'app_version' => $v['app_version'] ?? null,
            'fcm_token' => $v['fcm_token'] ?? null,
            'notification_access' => $v['notification_access'] ?? $v['has_notification_permission'] ?? $v['permission'] ?? null,
            'battery_optimization_ignored' => $v['battery_optimization_ignored'] ?? null,
        ], fn ($x) => $x !== null));
        $device->last_seen_at = now();
        $device->save();

        $state = $device->states()->create([
            'has_notification_permission' => (bool) ($v['has_notification_permission'] ?? $v['permission'] ?? $device->notification_access),
            'battery_level' => $v['battery_level'] ?? $v['battery'] ?? null,
            'is_charging' => (bool) ($v['is_charging'] ?? false),
            'network_type' => $v['network_type'] ?? $v['network'] ?? null,
            'latitude' => $v['latitude'] ?? $v['lat'] ?? null,
            'longitude' => $v['longitude'] ?? $v['lng'] ?? null,
            'location_accuracy' => $v['location_accuracy'] ?? $v['accuracy'] ?? null,
            'reported_at' => isset($v['reported_at']) ? $v['reported_at'] : now(),
        ]);

        return response()->json(['data' => [
            'device' => new DeviceResource($device),
            'state' => [
                'id' => $state->id,
                'reported_at' => $state->reported_at?->toIso8601ZuluString(),
            ],
        ]]);
    }
}
