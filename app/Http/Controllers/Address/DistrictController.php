<?php

namespace App\Http\Controllers\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\CreateDistrictRequest;
use App\Http\Requests\Address\UpdateDistrictRequest;
use App\Models\Address\District;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = District::query();
        $districts = QueryHelper::applyQuery($query, $request, ['name','code']);
        return ApiResponse::success($districts, ApiMessage::ADDRESS_DISTRICT_LIST);
    }

    public function store(CreateDistrictRequest $request): JsonResponse
    {
        $district = District::create($request->validated());
        return ApiResponse::success($district, ApiMessage::ADDRESS_DISTRICT_STORE_SUCCESS);
    }

    public function update($id, UpdateDistrictRequest $request): JsonResponse
    {
        try {
            $district = District::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "district_id" => $id,
                "request_body" => $request->all(),
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_DISTRICT_NOT_FOUND, 404);
        }
        // get only value not null in request

        $district->update($request->validated());
        return ApiResponse::success($district, ApiMessage::ADDRESS_DISTRICT_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $district = District::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "district_id" => $id,
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_DISTRICT_NOT_FOUND, 404);
        }
        $district->delete();
        return ApiResponse::success($district, ApiMessage::ADDRESS_DISTRICT_DESTROY_SUCCESS);
    }
}
