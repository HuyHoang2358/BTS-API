<?php

namespace App\Http\Controllers\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\CreateCountryRequest;
use App\Http\Requests\Address\CreateProvinceRequest;
use App\Http\Requests\Address\UpdateCountryRequest;
use App\Http\Requests\Address\UpdateProvinceRequest;
use App\Models\Address\Country;
use App\Models\Address\Province;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Province::query();
        $provinces = QueryHelper::applyQuery($query, $request, ['name','code']);
        return ApiResponse::success($provinces, ApiMessage::ADDRESS_PROVINCE_LIST, 200);
    }

    public function store(CreateProvinceRequest $request): JsonResponse
    {
        $province = Province::create($request->validated());
        return ApiResponse::success($province, ApiMessage::ADDRESS_PROVINCE_STORE_SUCCESS, 200);
    }

    public function update($id, UpdateProvinceRequest $request): JsonResponse
    {
        try {
            $province = Province::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "province_id" => $id,
                "request_body" => $request->all(),
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_PROVINCE_NOT_FOUND, 404);
        }
        // get only value not null in request

        $province->update($request->validated());
        return ApiResponse::success($province, ApiMessage::ADDRESS_PROVINCE_UPDATE_SUCCESS, 200);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $province = Province::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "province_id" => $id,
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_PROVINCE_NOT_FOUND, 404);
        }
        $province->delete();
        return ApiResponse::success($province, ApiMessage::ADDRESS_PROVINCE_DESTROY_SUCCESS, 200);
    }
}
