<?php

namespace App\Http\Requests;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'type_user_id' => ['required', 'integer', 'exists:type_users,id', Rule::in([UserType::Administrator->value])], // Permitir solo el tipo de usuario "Administrador"
            'ci' => 'required|string|max:30|unique:users,ci,' . $userId,
            'name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'email' => 'nullable|string|max:100|email',
            'password' => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:100',
            'photo' => 'nullable|string|max:100',
            'email_verified_status' => 'nullable|boolean',
        ];
    }
}
