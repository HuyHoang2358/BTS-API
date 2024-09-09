<?php

namespace App\Http\Requests;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateWindyAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:5|unique:windy_areas,name',
            'wo' => 'nullable|integer',
            'v3s50' => 'nullable|integer',
            'v10m50' => 'nullable|integer',
            'description' => 'nullable'
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.windy-area.name.required'),
                'max' => __('customValidate.windy-area.name.max'),
                'unique' => __('customValidate.windy-area.name.unique'),
            ],
            'wo' => [
                'integer' => __('customValidate.windy-area.wo.integer'),
            ],
            'v3s50' => [
                'integer' => __('customValidate.windy-area.v3s50.integer'),
            ],
            'v10m50' => [
                'integer' => __('customValidate.windy-area.v10m50.integer'),
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
