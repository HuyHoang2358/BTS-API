<?php
namespace App\Helpers;

use App\Enums\ApiMessage;
use App\Exports\FailExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ExcelHelper
{
    public static function validateFileFormat($file, $requiredHeadings): array
    {
        $headings = Excel::toArray((object)null, $file)[0][0];
        // lower headings
        $headings = array_map('strtolower', $headings);



        $ok = true;
        $errors = [];
        foreach ($requiredHeadings as $requiredHeading) {
            if (!in_array($requiredHeading, $headings)) {
                $ok = false;
                $errors[] = __('customValidate.excel.column') ." ". $requiredHeading. " ". __('customValidate.excel.exist');
            }
        }
        return ["ok" => $ok, "errors" => $errors, "headings" => $headings];
    }
    public static function importExcel($file, $import, $successMessage): JsonResponse
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = ".".$file->getClientOriginalExtension();
        $userId = auth()->id() ?? 0;
        $file->storeAs('public/temp/'.$userId.'/upload/', $filename."_".time().$fileExtension);

        // import excel data to database
        $import->import(request()->file('file'));
        // get failures data
        $failureData = [];
        foreach ($import->failures() as $failure) {
            $failureData[] = [
                'row'       => $failure->row(), // The row that failed
                'column' => $failure->attribute(), // The column name or index that failed
                'errors'    => $failure->errors(), // Validation error messages
                'values'    => array_filter($failure->values(), function ($value) {return $value !== null;}),
            ];
        }
        if (count($failureData) > 0) {
            $filePath = 'public/temp/'.$userId.'/export/import_fail_'.$filename."_".time().'.xlsx';
            Excel::store(new FailExport($import->failures(), $file), $filePath);
            return ApiResponse::error(['file' => url(Storage::url($filePath)), 'errors' => $failureData], ApiMessage::VALIDATE_FAIL, 422);

            /* Encode the file content to base64
             * $base64 = base64_encode($file);
             * return ApiResponse::error(['file' => $base64, 'errors' => $failureData], ApiMessage::VALIDATE_FAIL, 422);
            */

            //return Excel::download(new FailExport($import->failures(), $request->file('file')), 'import_failures.xlsx');
        }
        return ApiResponse::success($failureData, $successMessage);
    }

}
