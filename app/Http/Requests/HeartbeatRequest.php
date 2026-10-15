<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeartbeatRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'has_notification_permission' => 'nullable|boolean',
            'permission' => 'nullable|boolean',
            'battery_level' => 'nullable|integer|min:0|max:100',
            'battery' => 'nullable|integer|min:0|max:100',
            'is_charging' => 'nullable|boolean',
            'network_type' => 'nullable|string|max:32',
            'network' => 'nullable|string|max:32',
            'latitude' => 'nullable|numeric|between:-90,90',
            'lat' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'lng' => 'nullable|numeric|between:-180,180',
            'location_accuracy' => 'nullable|numeric',
            'accuracy' => 'nullable|numeric',
            'reported_at' => 'nullable|date',
            'app_version' => 'nullable|string|max:64',
            'fcm_token' => 'nullable|string|max:512',
            'notification_access' => 'nullable|boolean',
            'battery_optimization_ignored' => 'nullable|boolean',
        ];
    }
}
