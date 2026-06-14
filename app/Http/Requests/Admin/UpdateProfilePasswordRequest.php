<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfilePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Informe sua senha atual.',
            'current_password.current_password' => 'A senha atual esta incorreta.',
            'password.required' => 'Informe a nova senha.',
            'password.confirmed' => 'A confirmacao da nova senha nao confere.',
        ];
    }
}
