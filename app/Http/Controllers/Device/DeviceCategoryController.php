<?php

namespace App\Http\Controllers\Device;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\CreateDeviceCategoryRequest;
use App\Http\Requests\Device\UpdateDeviceCategoryRequest;
use App\Models\Device\DeviceCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = DeviceCategory::query();
        $deviceCategories = QueryHelper::applyQuery($query, $request, ['name']);
        return ApiResponse::success($deviceCategories, ApiMessage::DEVICE_CATEGORY_LIST);
    }

    public function store(CreateDeviceCategoryRequest $request): JsonResponse
    {
        $deviceCategory = DeviceCategory::create($request->validated());
        return ApiResponse::success($deviceCategory, ApiMessage::DEVICE_CATEGORY_STORE_SUCCESS);
    }

    public function update($id, UpdateDeviceCategoryRequest $request): JsonResponse
    {
        try {
            $deviceCategory = DeviceCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
                "suggest_message" => 'Hãy kiểm tra lại id danh mục thiết bị',
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_CATEGORY_NOT_FOUND, 404);
        }
        // get only value not null in request

        $deviceCategory->update($request->validated());
        return ApiResponse::success($deviceCategory, ApiMessage::DEVICE_CATEGORY_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $deviceCategory = DeviceCategory::findOrFail($id);

        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "error" => $e->getMessage(),
                "suggest_message" => 'Hãy kiểm tra lại id danh mục thiết bị',
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_CATEGORY_NOT_FOUND, 404);
        }
        $count = $deviceCategory->devices()->count();
        if ($count > 0) {
            $errors = [
                "device_category_id" => $id,
                "device_count" => $count,
                'suggest_message' => 'Hãy xóa các thiết bị thuộc danh mục này trước khi xóa danh mục',
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_CATEGORY_HAS_DEVICE, 400);
        }
        $deviceCategory->delete();
        return ApiResponse::success($deviceCategory, ApiMessage::DEVICE_CATEGORY_DESTROY_SUCCESS);
    }
}
