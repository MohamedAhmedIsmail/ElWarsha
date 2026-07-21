<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\LoginUserData;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
        ];
    }

    public function toDto(): LoginUserData
    {
        return new LoginUserData(
            phone: $this->validated('phone'),
            password: $this->validated('password'),
        );
    }
}
