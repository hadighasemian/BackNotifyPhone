<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class Device extends Model
{
    use HasApiTokens;

    public const ONLINE_MINUTES = 5;

    protected $fillable = [
        'device_uuid', 'token', 'brand', 'model', 'manufacturer', 'os_version', 'sdk_int',
        'app_version', 'fcm_token', 'label', 'notification_access', 'battery_optimization_ignored', 'last_seen_at',
    ];
    protected $hidden = ['token', 'fcm_token'];

    protected function casts(): array
    {
        return [
            'sdk_int' => 'integer',
            'notification_access' => 'boolean',
            'battery_optimization_ignored' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function states(): HasMany { return $this->hasMany(DeviceState::class); }
    public function latestState(): HasOne { return $this->hasOne(DeviceState::class)->latestOfMany('reported_at'); }
    public function latestLocatedState(): HasOne
    {
        return $this->hasOne(DeviceState::class)->ofMany(['reported_at' => 'max', 'id' => 'max'], fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'));
    }
    public function notifications(): HasMany { return $this->hasMany(Notification::class); }
    public function appErrors(): HasMany { return $this->hasMany(AppError::class); }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at !== null && $this->last_seen_at->gte(now()->subMinutes(self::ONLINE_MINUTES));
    }

    public function scopeOnline($q) { return $q->where('last_seen_at', '>=', now()->subMinutes(self::ONLINE_MINUTES)); }
    public function scopeOffline($q)
    {
        return $q->where(fn ($w) => $w->whereNull('last_seen_at')->orWhere('last_seen_at', '<', now()->subMinutes(self::ONLINE_MINUTES)));
    }
}
