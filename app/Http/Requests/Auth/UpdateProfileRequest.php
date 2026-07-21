<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\UpdateProfileData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'city' => ['sometimes', 'nullable', 'string', 'max:100'],
            'area' => ['sometimes', 'nullable', 'string', 'max:100'],
            'avatar' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }

    public function toDto(): UpdateProfileData
    {
        return new UpdateProfileData(
            name: $this->validated('name'),
            email: $this->validated('email'),
            city: $this->validated('city'),
            area: $this->validated('area'),
            avatar: $this->validated('avatar'),
        );
    }
}
