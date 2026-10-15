<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'device_uuid' => $this->device_uuid, 'brand' => $this->brand, 'model' => $this->model,
            'manufacturer' => $this->manufacturer, 'os_version' => $this->os_version, 'sdk_int' => $this->sdk_int,
            'app_version' => $this->app_version, 'label' => $this->label, 'notification_access' => $this->notification_access,
            'battery_optimization_ignored' => $this->battery_optimization_ignored, 'has_fcm_token' => (bool) $this->fcm_token,
            'is_online' => $this->is_online, 'last_seen_at' => $this->last_seen_at?->toIso8601ZuluString(),
            'created_at' => $this->created_at?->toIso8601ZuluString(),
            'latest_state' => new DeviceStateResource($this->whenLoaded('latestState')),
            'notifications_count' => $this->whenCounted('notifications'),
            'app_errors_count' => $this->whenCounted('appErrors'),
            'states_count' => $this->whenCounted('states'),
        ];
    }
}
