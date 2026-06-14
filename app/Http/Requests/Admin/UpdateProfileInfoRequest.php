<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome e obrigatorio.',
            'email.required' => 'O e-mail e obrigatorio.',
            'email.email' => 'Informe um e-mail valido.',
            'email.unique' => 'Este e-mail ja esta em uso.',
        ];
    }
}
