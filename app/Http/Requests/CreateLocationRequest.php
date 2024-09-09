<?php

namespace App\Http\Requests;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'location_latitude' => 'required|numeric|between:-90,90',
            'location_longitude' => 'required|numeric|between:-180,180',
            'location_height' => 'required|numeric|max:1000',
        ];
    }
    public function messages(): array
    {
        return [
            'location_latitude' => [
                'required' => __('customValidate.station.location_latitude.required'),
                'numeric' => __('customValidate.station.location_latitude.numeric'),
                'between' => __('customValidate.station.location_latitude.between'),
            ],
            'location_longitude' => [
                'required' => __('customValidate.station.location_longitude.required'),
                'numeric' => __('customValidate.station.location_longitude.numeric'),
                'between' => __('customValidate.station.location_longitude.between'),
            ],
            'location_height' => [
                'required' => __('customValidate.station.location_height.required'),
                'numeric' => __('customValidate.station.location_height.numeric'),
                'max' => __('customValidate.station.location_height.max'),
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
