<?php

namespace App\Http\Requests\Admin;

use App\Rules\SafeImageUpload;
use Illuminate\Foundation\Http\FormRequest;

class StagePropertyImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', new SafeImageUpload()],
        ];
    }
}
