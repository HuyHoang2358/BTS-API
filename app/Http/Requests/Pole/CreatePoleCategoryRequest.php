<?php

namespace App\Http\Requests\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePoleCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:255|unique:pole_categories,name',
            'code' => 'required|max:255|unique:pole_categories,code',
            'description' => 'nullable|max:500'
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.pole.category.name.required'),
                'max' => __('customValidate.pole.category.name.max'),
                'unique' => __('customValidate.pole.category.name.unique'),
            ],
            'code' =>[
                'required' => __('customValidate.pole.category.code.required'),
                'max' => __('customValidate.pole.category.code.max'),
                'unique' => __('customValidate.pole.category.code.unique'),
            ],
            'description' => [
                'max' => __('customValidate.pole.category.description.max'),
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
