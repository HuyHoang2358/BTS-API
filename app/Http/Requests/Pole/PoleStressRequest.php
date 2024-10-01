<?php

namespace App\Http\Requests\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PoleStressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pole_id' => 'nullable|exists:poles,id',
            'devices' => 'required|array|min:1',
            //'devices.*.id' => 'required|exists:devices,id',
            'devices.*.name' => 'required|exists:devices,name',
            'devices.*.depth' => 'required|numeric|min:0',
            'devices.*.width' => 'required|numeric|min:0',
            'devices.*.height' => 'required|numeric|min:0',
            //'devices.*.weight' => 'required|numeric|min:0',
            'devices.*.DC' => 'required|numeric|between:0,100',
        ];
    }
    // Cấu hình các message tương ứng
    public function messages(): array
    {
        return [
            'station_code' => [
                'exists' => __('customValidate.pole.station_code.exists'),
            ],
            'devices' =>[
                'required' => __('customValidate.pole.devices.required'),
                'array' => __('customValidate.pole.devices.array'),
                'min' => __('customValidate.pole.devices.min'),
            ],
            'devices.*.id' => [
                'required' => __('customValidate.pole.devices.id.required'),
                'exists' => __('customValidate.pole.devices.id.exists'),
            ],
            'devices.*.name' => [
                'required' => __('customValidate.pole.devices.name.required'),
                'exists' => __('customValidate.pole.devices.name.exists'),
            ],
            'devices.*.depth' => [
                'required' => __('customValidate.pole.devices.depth.required'),
                'numeric' => __('customValidate.pole.devices.depth.numeric'),
                'min' => __('customValidate.pole.devices.depth.min'),
            ],
            'devices.*.width' => [
                'required' => __('customValidate.pole.devices.width.required'),
                'numeric' => __('customValidate.pole.devices.width.numeric'),
                'min' => __('customValidate.pole.devices.width.min'),
            ],
            'devices.*.height' => [
                'required' => __('customValidate.pole.devices.height.required'),
                'numeric' => __('customValidate.pole.devices.height.numeric'),
                'min' => __('customValidate.pole.devices.height.min'),
            ],
            'devices.*.weight' => [
                'required' => __('customValidate.pole.devices.weight.required'),
                'numeric' => __('customValidate.pole.devices.weight.numeric'),
                'min' => __('customValidate.pole.devices.weight.min'),
            ],
            'devices.*.DC' => [
                'required' => __('customValidate.pole.devices.DC.required'),
                'numeric' => __('customValidate.pole.devices.DC.integer'),
                'between' => __('customValidate.pole.devices.DC.between'),
            ]
        ];
    }


    // fail validate
    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::error($validator->errors(), ApiMessage::VALIDATE_FAIL, 422);
        throw new ValidationException($validator, $response);
    }

}

