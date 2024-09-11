<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class ProfilePictureRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
            {
                return [
                    'profile' => 'required|file',
                    'profile_original' => 'required|file',
                    'user_id' => 'required',
                    'crop_setting' => 'required',
                ];
            }
            default:
                return [];
        }
    }

    public function messages(): array
    {
        return [
            'profile.required' => 'La foto de perfil es requerida',
            'profile.image' => 'La foto de perfil debe ser una imagen válida',
            'profile_original.required' => 'La foto de perfil original es requerida',
            'profile_original.image' => 'La foto de perfil original debe ser una imagen válida',
            'user_id.required' => 'El ID de usuario es requerido',
            'user_id.exists' => 'El ID de usuario no existe en la base de datos',
            'crop_setting.required' => 'La configuración de recorte es requerida',
        ];
    }

}
