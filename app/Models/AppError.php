<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppError extends Model
{
    const UPDATED_AT = null;
    protected $fillable = ['device_id', 'level', 'message', 'stacktrace', 'app_version', 'os_version', 'context'];

    protected function casts(): array { return ['context' => 'array', 'created_at' => 'datetime']; }

    public function device() { return $this->belongsTo(Device::class); }
}
