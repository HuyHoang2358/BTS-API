<?php

namespace App\Http\Controllers\Pole;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pole\CreatePoleRequest;
use App\Http\Requests\Pole\UpdatePoleRequest;
use App\Models\Pole\Pole;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Pole::query();
        $poles = QueryHelper::applyQuery($query, $request, ['name'], ['category'], ['pole_category_id']);

        return ApiResponse::success($poles, ApiMessage::POLE_LIST);
    }

    public function store(CreatePoleRequest $request): JsonResponse
    {
        $pole = Pole::create($request->validated());
        return ApiResponse::success($pole, ApiMessage::POLE_STORE_SUCCESS);
    }

    public function update($id, UpdatePoleRequest $request): JsonResponse
    {
        try {
            $pole = Pole::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "pole_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        // get only value not null in request

        $pole->update($request->validated());

        return ApiResponse::success($pole, ApiMessage::POLE_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $pole = Pole::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::POLE_NOT_FOUND, 404);
        }
        $pole->delete();
        return ApiResponse::success($pole, ApiMessage::POLE_DESTROY_SUCCESS);
    }

}
