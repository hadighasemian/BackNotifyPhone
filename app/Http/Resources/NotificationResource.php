<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'device_id' => $this->device_id, 'device_notif_key' => $this->device_notif_key,
            'package_name' => $this->package_name, 'app_label' => $this->app_label, 'title' => $this->title, 'text' => $this->text,
            'subtext' => $this->subtext, 'posted_at' => $this->posted_at?->toIso8601ZuluString(),
            'received_at' => $this->received_at?->toIso8601ZuluString(), 'status' => $this->status, 'extras' => $this->extras,
            'device' => $this->whenLoaded('device', fn () => ['id' => $this->device->id, 'label' => $this->device->label, 'brand' => $this->device->brand, 'model' => $this->device->model])];
    }
}
