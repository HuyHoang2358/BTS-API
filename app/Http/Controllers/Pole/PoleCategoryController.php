<?php

namespace App\Http\Controllers\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pole\CreatePoleCategoryRequest;
use App\Http\Requests\Pole\UpdatePoleCategoryRequest;

use App\Models\Pole\PoleCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoleCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = PoleCategory::query();
        $poleCategories = QueryHelper::applyQuery($query, $request, ['name']);
        return ApiResponse::success($poleCategories, ApiMessage::POLE_CATEGORY_LIST);
    }

    public function store(CreatePoleCategoryRequest $request): JsonResponse
    {
        $poleCategory = PoleCategory::create($request->validated());
        return ApiResponse::success($poleCategory, ApiMessage::POLE_CATEGORY_STORE_SUCCESS);
    }

    public function update($id, UpdatePoleCategoryRequest $request): JsonResponse
    {
        try {
            $poleCategory = PoleCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "pole_category_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_CATEGORY_NOT_FOUND, 404);
        }
        // get only value not null in request

        $poleCategory->update($request->validated());
        return ApiResponse::success($poleCategory, ApiMessage::POLE_CATEGORY_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $poleCategory = PoleCategory::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_CATEGORY_NOT_FOUND, 404);
        }
        $count = $poleCategory->poles()->count();
        if ($count > 0) {
            $errors = [
                "device_category_id" => $id,
                "device_count" => $count,
                'suggest_message' => 'Hãy xóa tất cả các cột thuộc loại cột này trước khi xóa',
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_CATEGORY_HAS_DEVICE, 400);
        }
        $poleCategory->delete();
        return ApiResponse::success($poleCategory, ApiMessage::POLE_CATEGORY_DESTROY_SUCCESS);
    }
}
