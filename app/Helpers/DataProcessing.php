<?php
namespace App\Helpers;

use App\Enums\ApiMessage;
use Illuminate\Http\JsonResponse;
use Spatie\Image\Exceptions\CouldNotLoadImage;
use Spatie\Image\Image;

class DataProcessing
{
    public static function getImages($imagesFolder): array
    {
        $images = [];
        $files = scandir($imagesFolder);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..']) ||
                !in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'JPG', 'png', 'PNG'])) continue;
            $images[] = $file;
        }
        return $images;
    }

    public static function resizeImage($src_path, $dst_path, $target_width = 500, $target_height = 500): void
    {
        Image::load($src_path)
            ->width($target_width)
            ->height($target_height)
            ->optimize()
            ->save($dst_path);
    }

    public static function getMetadata($src_path, $metadata, $isOnlyGetFromJson = true): array
    {
        $data = [];
        $data['filename'] = $src_path;
        $data['width'] = $metadata['Width'] ?? null;
        $data['height'] = $metadata['Height'] ?? null;
        $data["GPS"]["GPSAltitude"] = $metadata['GPSAltitude'] ?? null;
        $data["GPS"]["GPSLatitude"] = $metadata['GPSLatitude'] ?? null;
        $data["GPS"]["GPSLongitude"] = $metadata['GPSLongitude'] ?? null;
        $data["GPS"]["GPSAltitudeRef"]  = $metadata['GPSAltitudeRef'] ?? null;
        $data["GPS"]["GPSLatitudeRef"] = $metadata["GPSLatitudeRef"] ?? null;
        $data["GPS"]["GPSLongitudeRef"]  = $metadata["GPSLongitudeRef"] ?? null;
        $data["camera"]["GimbalRollDegree"] = $metadata["IFD0"]["DateTime"] ?? null;
        $data["camera"]["GimbalYawDegree"] = $metadata["IFD0"]["DateTime"] ?? null;
        $data["camera"]["GimbalPitchDegree"] = $metadata["IFD0"]["DateTime"] ?? null;
        $data["camera"]["Make"] = null;
        $data["camera"]["Model"] = null;
        $data["camera"]["Software"] = null;
        $data["camera"]["DateTime"] = null;

        if ($isOnlyGetFromJson) return $data;

        $exifData = @exif_read_data($src_path, 0, true);
        // Get basic information
        $data["filename"] = $exifData["FILE"]["FileName"] ?? null;
        $data["width"] = $exifData["COMPUTED"]["Width"] ?? null;
        $data["height"] = $exifData["COMPUTED"]["Height"] ?? null;

        // get camera information
        $data["camera"]["Make"] = $exifData["IFD0"]["Make"] ?? null;
        $data["camera"]["Model"] = $exifData["IFD0"]["Model"] ?? null;
        $data["camera"]["Software"] = $exifData["IFD0"]["Software"] ?? null;
        $data["camera"]["DateTime"] = $exifData["IFD0"]["DateTime"] ?? null;

        // get GPS information
        $data["GPS"]["GPSLatitude"] = $exifData["GPS"]["GPSLatitude"] ?? null;
        $data["GPS"]["GPSLongitude"] = $exifData["GPS"]["GPSLongitude"] ?? null;
        $data["GPS"]["GPSAltitude"] = $exifData["GPS"]["GPSAltitude"] ?? null;
        $data["GPS"]["GPSAltitudeRef"] = $exifData["GPS"]["GPSAltitudeRef"] ?? null;
        $data["GPS"]["GPSLatitudeRef"] = $exifData["GPS"]["GPSLatitudeRef"] ?? null;
        $data["GPS"]["GPSLongitudeRef"] = $exifData["GPS"]["GPSLongitudeRef"] ?? null;
        //
        $data["exifData"] = $exifData;
        return $data;
    }

    public static function readMetadata(){};
}
