<?php

namespace App\Http\Controllers\Device;

use App\Enums\ApiMessage;
use App\Exports\DeviceExport;
use App\Helpers\ApiResponse;
use App\Helpers\ExcelHelper;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\CreateDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Http\Requests\ImportExcelFileRequest;
use App\Imports\DeviceImport;
use App\Models\Device\Device;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    protected function addOrUpdateParamsToDevice($device, $params_str): void
    {
        $device->params()->delete();
        if (empty($params_str)) return;

        $pairs = array_map('trim', explode(',', $params_str));
        foreach ($pairs as $pair) {
            if (!str_contains($pair, ':')) continue;
            list($key, $value) = explode(':', $pair, 2) + [null, null];
            if ($key !== null && $value !== null) {
                $key = trim($key);
                $value = trim($value);
                $device->params()->create([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    public function index(Request $request): JsonResponse
    {
        $query = Device::query();
        $devices = QueryHelper::applyQuery($query, $request, ['name'], ['category', 'vendor', 'params'], ['device_category_id', 'vendor_id']);

        return ApiResponse::success($devices, ApiMessage::DEVICE_LIST);
    }

    public function store(CreateDeviceRequest $request): JsonResponse
    {
        $device = Device::create($request->validated());
        $this->addOrUpdateParamsToDevice($device, $request->input('params', ''));

        return ApiResponse::success($device, ApiMessage::DEVICE_STORE_SUCCESS);
    }

    public function update($id, UpdateDeviceRequest $request): JsonResponse
    {
        try {
            $device = Device::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_NOT_FOUND, 404);
        }
        // get only value not null in request

        $device->update($request->validated());
        $this->addOrUpdateParamsToDevice($device, $request->input('params', ''));

        return ApiResponse::success($device, ApiMessage::DEVICE_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $device = Device::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "device_category_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_NOT_FOUND, 404);
        }
        $device->delete();
        return ApiResponse::success($device, ApiMessage::DEVICE_DESTROY_SUCCESS);
    }

    public function importExcel(ImportExcelFileRequest $request): JsonResponse
    {
        $requiredHeadings = ['name', 'vendor', 'category', 'length', 'width', 'depth', 'weight', 'diameter', 'description', 'params'];
        $import = new DeviceImport();
        $file = $request->file('file');
        $validate = ExcelHelper::validateFileFormat($file, $requiredHeadings);
        if (!$validate["ok"])
            return ApiResponse::error($validate, ApiMessage::FILE_FORMAT_FAIL, 422);

        //return ApiResponse::success($validate, ApiMessage::FILE_FORMAT_FAIL);
        return ExcelHelper::importExcel($file, $import, ApiMessage::DEVICE_IMPORT_SUCCESS);

    }

    public function exportExcel(): JsonResponse
    {
        $userId = auth()->id() ?? 0;
        $filePath = 'public/temp/'.$userId.'/export/'.'data_devices_'.time().'.xlsx';
        Excel::store(new DeviceExport(), $filePath);
        return ApiResponse::success(['file' => url(Storage::url($filePath))], ApiMessage::DEVICE_LIST);
    }
}
