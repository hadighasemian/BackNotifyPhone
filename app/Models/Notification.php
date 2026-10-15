<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;
    protected $fillable = ['device_id', 'device_notif_key', 'package_name', 'app_label', 'title', 'text', 'subtext', 'posted_at', 'received_at', 'status', 'extras'];

    protected function casts(): array
    {
        return ['posted_at' => 'datetime', 'received_at' => 'datetime', 'extras' => 'array'];
    }

    public function device() { return $this->belongsTo(Device::class); }
}
