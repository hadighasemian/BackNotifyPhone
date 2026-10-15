<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppVersionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'version_name' => $this->version_name, 'version_code' => $this->version_code,
            'file_size' => $this->file_size, 'sha256' => $this->sha256, 'release_notes' => $this->release_notes,
            'is_active' => $this->is_active, 'downloads' => $this->downloads, 'download_url' => $this->downloadUrl(),
            'created_at' => $this->created_at?->toIso8601ZuluString(), 'updated_at' => $this->updated_at?->toIso8601ZuluString()];
    }
}
