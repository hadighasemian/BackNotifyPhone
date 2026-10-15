<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDeviceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'device_uuid' => 'required|string|max:191',
            'brand' => 'nullable|string|max:191',
            'model' => 'nullable|string|max:191',
            'manufacturer' => 'nullable|string|max:191',
            'os_version' => 'nullable|string|max:64',
            'sdk_int' => 'nullable|integer',
            'app_version' => 'nullable|string|max:64',
            'fcm_token' => 'nullable|string|max:512',
            'label' => 'nullable|string|max:191',
            'notification_access' => 'nullable|boolean',
            'battery_optimization_ignored' => 'nullable|boolean',
        ];
    }
}
