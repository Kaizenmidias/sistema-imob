<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_photo.required' => 'Selecione uma imagem para o avatar.',
            'profile_photo.image' => 'O arquivo selecionado precisa ser uma imagem valida.',
            'profile_photo.mimes' => 'Use apenas JPG, JPEG, PNG ou WEBP.',
            'profile_photo.max' => 'A imagem deve ter no maximo 5 MB.',
        ];
    }
}
