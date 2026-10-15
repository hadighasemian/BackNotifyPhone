<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppVersionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'version_name' => 'sometimes|string|max:64',
            'release_notes' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
