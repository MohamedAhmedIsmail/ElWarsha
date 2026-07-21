<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\RegisterUserData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(UserRole::publicRegistrationValues())],
        ];
    }

    public function toDto(): RegisterUserData
    {
        return new RegisterUserData(
            name: $this->validated('name'),
            phone: $this->validated('phone'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            role: UserRole::from($this->validated('role')),
        );
    }
}
