<?php
namespace App\Helpers;

use App\Enums\ApiMessage;
use Illuminate\Http\JsonResponse;
class StorageHelper
{
    public function storeFile($file, $userId, $folder, $filename, $fileExtension)
    {
        $file->storeAs('public/'.$folder.'/'.$userId.'/upload/', $filename."_".time().$fileExtension);
    }
}
