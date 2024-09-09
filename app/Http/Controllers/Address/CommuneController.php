<?php

namespace App\Http\Controllers\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Address\CreateCommuneRequest;
use App\Http\Requests\Address\UpdateCommuneRequest;
use App\Models\Address\Commune;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommuneController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Commune::query();
        $communes = QueryHelper::applyQuery($query, $request, ['name','code']);
        return ApiResponse::success($communes, ApiMessage::ADDRESS_COMMUNE_LIST);
    }

    public function store(CreateCommuneRequest $request): JsonResponse
    {
        $commune = Commune::create($request->validated());
        return ApiResponse::success($commune, ApiMessage::ADDRESS_COMMUNE_STORE_SUCCESS);
    }

    public function update($id, UpdateCommuneRequest $request): JsonResponse
    {
        try {
            $commune = Commune::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "commune_id" => $id,
                "request_body" => $request->all(),
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_COMMUNE_NOT_FOUND, 404);
        }
        // get only value not null in request

        $commune->update($request->validated());
        return ApiResponse::success($commune, ApiMessage::ADDRESS_COMMUNE_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $commune = Commune::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "commune_id" => $id,
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::ADDRESS_COMMUNE_NOT_FOUND, 404);
        }
        $commune->delete();
        return ApiResponse::success($commune, ApiMessage::ADDRESS_COMMUNE_DESTROY_SUCCESS);
    }
}
