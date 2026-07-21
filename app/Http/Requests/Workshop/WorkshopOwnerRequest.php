<?php

namespace App\Http\Requests\Workshop;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

abstract class WorkshopOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->user()?->role;

        return $role instanceof UserRole
            ? $role === UserRole::WorkshopOwner
            : $role === UserRole::WorkshopOwner->value;
    }
}
