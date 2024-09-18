<?php

namespace App\Http\Controllers;

use App\Enums\ApiMessage;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UploadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function uploadFile($type, Request $request): JsonResponse
    {
        $file = $request->file('file');
        $path = '';
        switch ($type) {
            case 'image':
                // validate image
                try {
                    $this->validate($request, [
                        'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    ]);
                } catch (ValidationException $e) {
                    return ApiResponse::error(['e' => $e->getMessage()], ApiMessage::UPLOAD_TYPE_NOT_SUPPORT, 404);
                }
                $path = 'public/upload/image/';
                break;
            default:
                return ApiResponse::error([], ApiMessage::UPLOAD_TYPE_NOT_SUPPORT, 404);
        }

        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExtension = ".".$file->getClientOriginalExtension();

        $new_filename = Str::slug($filename)."_".time().$fileExtension;
        $file->storeAs($path, $new_filename);
        $url = Storage::url($path.$new_filename);
        return ApiResponse::success(['url' => $url], ApiMessage::UPLOAD_SUCCESS);
    }
}
