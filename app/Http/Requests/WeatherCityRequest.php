<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rules\Password;

class WeatherCityRequest extends FormRequest
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
            'city' => 'required|string|max:150',
            'lang' => 'string|max:2|min:2'
        ];
    }

    public function messages()
    {
        return [
            'city.required' => __('messages.validation_errors.city_required'),
            'city.max' => __('messages.validation_errors.invalid_city_max_length'),
            'lang.max' => __('messages.validation_errors.invalid_lang_length'),
            'lang.min' => __('messages.validation_errors.invalid_lang_length'),
            'lang.string' => __('messages.validation_errors.lang_must_be_string'),
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
