<?php

namespace App\Http\Requests\Auth;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    // dùng để check quyền
    public function authorize(): bool
    {
        return true;
    }

    // Khai báo rule cho các field
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ];
    }
    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'email.required' => __('customValidate.email.required'),
            'email.email' => __('customValidate.email.valid'),
            'email.exists' => __('customValidate.email.exist'),
            'password.required' => __('customValidate.password.required'),
        ];
    }

    // fail validate
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::ERROR, 422);
        throw new ValidationException($validator, $response);
    }
}
