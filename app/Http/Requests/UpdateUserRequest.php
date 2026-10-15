<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('user')?->id ?? $this->route('user');
        return [
            'name' => 'sometimes|string|max:191',
            'email' => 'sometimes|email|max:191|unique:users,email,'.$id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:admin',
        ];
    }
}
