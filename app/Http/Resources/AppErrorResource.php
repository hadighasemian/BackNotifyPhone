<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppErrorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'device_id' => $this->device_id, 'level' => $this->level, 'message' => $this->message,
            'stacktrace' => $this->stacktrace, 'app_version' => $this->app_version, 'os_version' => $this->os_version,
            'context' => $this->context, 'created_at' => $this->created_at?->toIso8601ZuluString(),
            'device' => $this->whenLoaded('device', fn () => $this->device ? ['id' => $this->device->id, 'label' => $this->device->label, 'brand' => $this->device->brand, 'model' => $this->device->model] : null)];
    }
}
