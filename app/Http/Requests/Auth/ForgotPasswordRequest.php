<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;

class ForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            "email" => "required|email"
        ];
    }

    public function messages(): array
    {
        return [
            "email.required" => "El email es requerido",
            "email.email"   => "El email es inválido"
        ];
    }

    public function response(array $errors): JsonResponse
    {
        return response()->json(["status" => false, "message" => "validations params failed", "validations" => $errors], 400);

    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException( response()->json(["status" => false, "message" => "Parámetros inválidos", "validations" => $validator->errors()], 422));

    }
}
