<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceState extends Model
{
    public $timestamps = false;
    protected $fillable = ['device_id', 'has_notification_permission', 'battery_level', 'is_charging', 'network_type', 'latitude', 'longitude', 'location_accuracy', 'reported_at'];

    protected function casts(): array
    {
        return [
            'has_notification_permission' => 'boolean', 'is_charging' => 'boolean', 'battery_level' => 'integer',
            'latitude' => 'float', 'longitude' => 'float', 'location_accuracy' => 'float', 'reported_at' => 'datetime',
        ];
    }

    public function device() { return $this->belongsTo(Device::class); }
}
