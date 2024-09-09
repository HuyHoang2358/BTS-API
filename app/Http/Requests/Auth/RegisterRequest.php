<?php

namespace App\Http\Requests\Auth;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.user.name.required'),
                'string' => __('customValidate.user.name.string'),
                'between' => __('customValidate.user.name.between'),
            ],
            'email' => [
                'required' => __('customValidate.user.email.required'),
                'email' => __('customValidate.user.email.valid'),
                'unique' => __('customValidate.user.email.unique'),
            ],
            'password' => [
                'required' => __('customValidate.user.password.required'),
                'confirmed' => __('customValidate.user.password.confirmed'),
                'min' => __('customValidate.user.password.min'),
            ],
        ];
    }


    // fail validate
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::VALIDATE_FAIL, 422);
        throw new ValidationException($validator, $response);
    }
}
