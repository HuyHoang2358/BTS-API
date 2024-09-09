<?php

namespace App\Http\Requests\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreatePoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:500|unique:poles,name',
            'height' => 'required|numeric|max:1000',
            'is_roof' => 'required|boolean',
            'house_height' => 'nullable|numeric|max:1000',
            'pole_category_id' => 'required|exists:pole_categories,id',
            'size' => 'nullable|string|max:255',
            'diameter_body_tube' => 'nullable|string|max:255',
            'structure' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'name' =>[
                'required' => __('customValidate.pole.name.required'),
                'max' => __('customValidate.pole.name.max'),
                'unique' => __('customValidate.pole.name.unique'),
            ],
            'height' => [
                'required' => __('customValidate.pole.height.required'),
                'numeric' => __('customValidate.pole.height.numeric'),
                'max' => __('customValidate.pole.height.max'),
            ],
            'is_roof' => [
                'required' => __('customValidate.pole.is_roof.required'),
                'boolean' => __('customValidate.pole.is_roof.boolean'),
            ],
            'house_height' => [
                'numeric' => __('customValidate.pole.house_height.numeric'),
                'max' => __('customValidate.pole.house_height.max'),
            ],
            'pole_category_id' => [
                'required' => __('customValidate.pole.pole_category_id.required'),
                'exists' => __('customValidate.pole.pole_category_id.exists'),
            ],
            'size' => [
                'string' => __('customValidate.pole.size.string'),
                'max' => __('customValidate.pole.size.max'),
            ],
            'diameter_body_tube' => [
                'string' => __('customValidate.pole.diameter_body_tube.string'),
                'max' => __('customValidate.pole.diameter_body_tube.max'),
            ],
            'structure' => [
                'string' => __('customValidate.pole.structure.string'),
                'max' => __('customValidate.pole.structure.max'),
            ],
            'description' => [
                'string' => __('customValidate.pole.description.string'),
                'max' => __('customValidate.pole.description.max'),
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
