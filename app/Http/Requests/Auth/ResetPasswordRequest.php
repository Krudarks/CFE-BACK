<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:4',
        ];
    }

    public function messages()
    {
        return [
            'token.required' => 'Token requerido',
            'email.required' => 'Email requerido',
            'password.required' => 'Password requerido',
            'password.min' => 'El password debe ser de al menos 4 caracteres',
            'password.confirmed' => 'El password no coincide',
        ];
    }

    public function response(array $errors){
        return response()->json(["status" => false, "message" => "validations params failed", "validations" => $errors], 400);

    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException( response()->json(["status" => false, "message" => "Parámetros inválidos", "validations" => $validator->errors()], 422));

    }
}
