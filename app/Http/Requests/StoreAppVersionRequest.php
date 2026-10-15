<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppVersionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'apk' => 'required|file|max:131072',
            'version_name' => 'required|string|max:64',
            'version_code' => 'required|integer|min:1|unique:app_versions,version_code',
            'release_notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
