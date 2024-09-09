<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Exports\FailExport;
use App\Exports\WindyAreaExport;
use App\Helpers\ApiResponse;
use App\Helpers\ExcelHelper;
use App\Helpers\QueryHelper;
use App\Http\Requests\CreateWindyAreaRequest;
use App\Http\Requests\ImportExcelFileRequest;
use App\Http\Requests\UpdateWindyAreaRequest;
use App\Imports\WindyAreaImport;
use App\Models\WindyArea;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class WindyAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = WindyArea::query();
        $windyAreas = QueryHelper::applyQuery($query, $request, ['name']);
        return ApiResponse::success($windyAreas, ApiMessage::WINDY_AREA_LIST);
    }

    public function store(CreateWindyAreaRequest $request): JsonResponse
    {
        $windyArea = WindyArea::create($request->validated());
        return ApiResponse::success($windyArea, ApiMessage::WINDY_AREA_STORE_SUCCESS);
    }

    public function update($id, UpdateWindyAreaRequest $request): JsonResponse
    {
        try {
            $windyArea = WindyArea::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "windy_area_id" => $id,
                "request_body" => $request->all(),
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::WINDY_AREA_NOT_FOUND, 404);
        }
        // get only value not null in request

        $windyArea->update($request->validated());
        return ApiResponse::success($windyArea, ApiMessage::WINDY_AREA_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $windyArea = WindyArea::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "windy_area_id" => $id,
                "e" => $e,
            ];
            return ApiResponse::error($errors, ApiMessage::WINDY_AREA_NOT_FOUND, 404);
        }
        $windyArea->delete();
        return ApiResponse::success($windyArea, ApiMessage::WINDY_AREA_DESTROY_SUCCESS);
    }

    public function importExcel(ImportExcelFileRequest $request): JsonResponse
    {
        $requiredHeadings = ['name', 'wo', 'v3s50', 'v10m50', 'description'];
        $import = new WindyAreaImport();
        $file = $request->file('file');
        $validate = ExcelHelper::validateFileFormat($file, $requiredHeadings);
        if (!$validate["ok"])
            return ApiResponse::error($validate, ApiMessage::FILE_FORMAT_FAIL, 422);

        return ExcelHelper::importExcel($file, $import, ApiMessage::DEVICE_IMPORT_SUCCESS);
   }

    public function exportExcel(): JsonResponse
    {
        $userId = auth()->id() ?? 0;
        $filePath = 'public/temp/'.$userId.'/export/'.'data_windy_areas_'.time().'.xlsx';
        Excel::store(new WindyAreaExport(), $filePath);
        return ApiResponse::success(['file' => url(Storage::url($filePath))], ApiMessage::WINDY_AREA_LIST);
    }
}
