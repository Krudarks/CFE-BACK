<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class BaseFormRequest extends FormRequest
{

    /**
     * Custom response for validation errors.
     *
     * @param array $errors
     * @return JsonResponse
     */
    public function response(array $errors): JsonResponse
    {
        return response()->json(["status" => false, "message" => "Falló en validaciones", "validations" => $errors], 400);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(["status" => false, "message" => "Error en validaciones", "validations" => $validator->errors()], 422));
    }
}
