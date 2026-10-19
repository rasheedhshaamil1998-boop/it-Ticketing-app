<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'record_card_number' => ['required', 'string', 'max:50', Rule::unique('users', 'record_card_number')->ignore($this->route('user'))],
            'role' => ['required', 'string', Rule::in(['superadmin', 'admin', 'user'])],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
