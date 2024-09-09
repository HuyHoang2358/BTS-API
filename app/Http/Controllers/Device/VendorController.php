<?php

namespace App\Http\Controllers\Device;

use App\Enums\ApiMessage;
use App\Exports\VendorExport;
use App\Exports\WindyAreaExport;
use App\Helpers\ApiResponse;
use App\Helpers\ExcelHelper;
use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Device\CreateVendorRequest;
use App\Http\Requests\Device\UpdateVendorRequest;
use App\Http\Requests\ImportExcelFileRequest;
use App\Imports\VendorImport;
use App\Models\Device\Vendor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Vendor::query();
        $vendors = QueryHelper::applyQuery($query, $request, ['name']);
        return ApiResponse::success($vendors, ApiMessage::DEVICE_VENDOR_LIST);
    }

    public function store(CreateVendorRequest $request): JsonResponse
    {
        $vendor = Vendor::create($request->validated());
        return ApiResponse::success($vendor, ApiMessage::DEVICE_VENDOR_STORE_SUCCESS);
    }

    public function update($id, UpdateVendorRequest $request): JsonResponse
    {
        try {
            $vendor = Vendor::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "vendor_id" => $id,
                "request_body" => $request->all(),
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_VENDOR_NOT_FOUND, 404);
        }
        // get only value not null in request

        $vendor->update($request->validated());
        return ApiResponse::success($vendor, ApiMessage::DEVICE_VENDOR_UPDATE_SUCCESS);
    }

    public function destroy($id): JsonResponse
    {
        try {
            $vendor = Vendor::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            $errors = [
                "vendor_id" => $id,
                "error" => $e->getMessage(),
            ];
            return ApiResponse::error($errors, ApiMessage::DEVICE_VENDOR_NOT_FOUND, 404);
        }
        $vendor->delete();
        return ApiResponse::success($vendor, ApiMessage::DEVICE_VENDOR_DESTROY_SUCCESS);
    }

    public function importExcel(ImportExcelFileRequest $request): JsonResponse
    {
        $requiredHeadings = ['name', 'description', 'website', 'logo'];
        $import = new VendorImport();
        $file = $request->file('file');
        $validate = ExcelHelper::validateFileFormat($file, $requiredHeadings);
        if (!$validate["ok"])
            return ApiResponse::error($validate, ApiMessage::FILE_FORMAT_FAIL, 422);

        return ExcelHelper::importExcel($file, $import, ApiMessage::DEVICE_IMPORT_SUCCESS);
    }

    public function exportExcel(): JsonResponse
    {
        $userId = auth()->id() ?? 0;
        $filePath = 'public/temp/'.$userId.'/export/'.'data_device_vendor'.time().'.xlsx';
        Excel::store(new VendorExport(), $filePath);
        return ApiResponse::success(['file' => url(Storage::url($filePath))], ApiMessage::DEVICE_VENDOR_LIST);
    }


}


