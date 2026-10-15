<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreErrorRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'level' => 'nullable|in:error,warn,fatal',
            'message' => 'required|string',
            'stacktrace' => 'nullable|string',
            'app_version' => 'nullable|string|max:191',
            'os_version' => 'nullable|string|max:191',
            'context' => 'nullable|array',
        ];
    }
}
