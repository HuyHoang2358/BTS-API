<?php

namespace App\Http\Requests\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdatePoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $poleId = $this->route('id');
        return [
            'name' => 'required|max:500|unique:poles,name,' . $poleId,
            'station_code' => 'nullable|exists:stations,code',
            'height' => 'nullable|numeric|max:1000',
            'is_roof' => 'nullable|boolean',
            'house_height' => 'nullable|numeric|max:1000',
            'pole_category_id' => 'nullable|exists:pole_categories,id',
            'size' => 'nullable|string|max:255',
            'diameter_body_tube' => 'nullable|string|max:50',
            'diameter_strut_tube' => 'nullable|string|max:50',
            'diameter_top_tube' => 'nullable|string|max:50',
            'diameter_bottom_tube' => 'nullable|string|max:50',
            'foot_size' => 'nullable|string|max:50',
            'top_size' => 'nullable|string|max:50',
            'structure' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'params' => 'nullable|array',
            'params.*.key' => 'nullable|string|max:255',
            'params.*.value' => 'nullable|string|max:255',
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
            'station_code' => [
                'exists' => __('customValidate.pole.station_code.exists'),
            ],
            'height' => [
                'numeric' => __('customValidate.pole.height.numeric'),
                'max' => __('customValidate.pole.height.max'),
            ],
            'is_roof' => [
                'boolean' => __('customValidate.pole.is_roof.boolean'),
            ],
            'house_height' => [
                'numeric' => __('customValidate.pole.house_height.numeric'),
                'max' => __('customValidate.pole.house_height.max'),
            ],
            'pole_category_id' => [
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
            'diameter_strut_tube' => [
                'string' => __('customValidate.pole.diameter_strut_tube.string'),
                'max' => __('customValidate.pole.diameter_strut_tube.max'),
            ],
            'diameter_top_tube' => [
                'string' => __('customValidate.pole.diameter_top_tube.string'),
                'max' => __('customValidate.pole.diameter_top_tube.max'),
            ],
            'diameter_bottom_tube' => [
                'string' => __('customValidate.pole.diameter_bottom_tube.string'),
                'max' => __('customValidate.pole.diameter_bottom_tube.max'),
            ],
            'foot_size' => [
                'string' => __('customValidate.pole.foot_size.string'),
                'max' => __('customValidate.pole.foot_size.max'),
            ],
            'top_size' => [
                'string' => __('customValidate.pole.top_size.string'),
                'max' => __('customValidate.pole.top_size.max'),
            ],
            'structure' => [
                'string' => __('customValidate.pole.structure.string'),
                'max' => __('customValidate.pole.structure.max'),
            ],
            'description' => [
                'string' => __('customValidate.pole.description.string'),
                'max' => __('customValidate.pole.description.max'),
            ],
            'params' => [
                'array' => __('customValidate.pole.params.array'),
                '*.key' => [
                    'string' => __('customValidate.pole.params.key.string'),
                    'max' => __('customValidate.pole.params.key.max'),
                ],
                '*.value' => [
                    'string' => __('customValidate.pole.params.value.string'),
                    'max' => __('customValidate.pole.params.value.max'),
                ]
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
