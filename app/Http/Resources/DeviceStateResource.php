<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceStateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'device_id' => $this->device_id, 'has_notification_permission' => $this->has_notification_permission,
            'battery_level' => $this->battery_level, 'is_charging' => $this->is_charging, 'network_type' => $this->network_type,
            'latitude' => $this->latitude, 'longitude' => $this->longitude, 'location_accuracy' => $this->location_accuracy,
            'reported_at' => $this->reported_at?->toIso8601ZuluString()];
    }
}
