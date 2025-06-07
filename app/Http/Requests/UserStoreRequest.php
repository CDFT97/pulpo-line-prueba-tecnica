<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8) // Debe tener por lo menos 8 caracteres
                            ->mixedCase() // Debe tener mayúsculas + minúsculas
                            ->letters() // Debe incluir letras
                            ->numbers() // Debe incluir números
                            ->symbols(), // Debe incluir símbolos,
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('messages.validation_errors.name_required'),
            'name.max' => __('messages.validation_errors.invalid_name_max_length'),
            'name.string' => __('messages.validation_errors.name_must_be_string'),
            'email.required' => __('messages.validation_errors.email_required'),
            'email.max' => __('messages.validation_errors.invalid_email_max_length'),
            'email.email' => __('messages.validation_errors.email_must_be_valid'),
            'email.unique' => __('messages.validation_errors.email_must_be_unique'),
            'password.required' => __('messages.validation_errors.password_required'),
            'password.confirmed' => __('messages.validation_errors.passwords_must_match'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors' => $validator->errors(),
            'status' => true
        ], 422));
    }
}
