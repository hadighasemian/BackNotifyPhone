<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    protected $fillable = ['version_name', 'version_code', 'file_path', 'file_size', 'sha256', 'release_notes', 'is_active', 'downloads'];
    protected $hidden = ['file_path'];

    protected function casts(): array
    {
        return ['version_code' => 'integer', 'file_size' => 'integer', 'is_active' => 'boolean', 'downloads' => 'integer'];
    }

    public static function latestActive(): ?self
    {
        return static::where('is_active', true)->orderByDesc('version_code')->first();
    }

    public function downloadUrl(): string
    {
        return url('/api/v1/app/download/'.$this->version_code);
    }
}
