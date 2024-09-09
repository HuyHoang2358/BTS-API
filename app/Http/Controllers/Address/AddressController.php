<?php

namespace App\Http\Controllers\Address;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use App\Helpers\ExcelHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImportExcelFileRequest;
use App\Imports\AddressImport;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    public function importExcel(ImportExcelFileRequest $request): JsonResponse
    {
        $requiredHeadings = ['tenTinh', 'maTinh', 'tenHuyen', 'maHuyen', 'tenXax', 'maXa'];
        $import = new Addressimport();
        $file = $request->file('file');
        $validate = ExcelHelper::validateFileFormat($file, $requiredHeadings);
        if (!$validate["ok"])
            return ApiResponse::error($validate, ApiMessage::FILE_FORMAT_FAIL, 422);

        return ExcelHelper::importExcel($file, $import, ApiMessage::DEVICE_IMPORT_SUCCESS);

    }
}
