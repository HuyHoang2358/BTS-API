<?php
namespace App\Helpers;

use Imagick;
use ImagickException;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class DataProcessing
{
    public static function getImages($imagesFolder): array
    {
        $images = [];
        $files = scandir($imagesFolder);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..']) ||
                !in_array(pathinfo($file, PATHINFO_EXTENSION), ['jpg', 'JPG', 'png', 'PNG'])) continue;
            $imageKey = pathinfo($file, PATHINFO_FILENAME);
            $images[] = $imageKey;
        }
        return $images;
    }

    /**
     * @throws ImagickException
     */
    public static function createThumbnail($src_path, $dst_path): void
    {
        if (file_exists($src_path)) return;
        $manager = new ImageManager(new Driver());
        $manager->read($src_path)
            ->scale(200)
            ->encode(new  WebpEncoder(90))
            ->save($dst_path);

        $img = new Imagick($dst_path);
        $img->stripImage();
        $img->writeImage($dst_path);
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

        $data["camera"]["GimbalRollDegree"] = $metadata["GimbalRollDegree"] ?? null;
        $data["camera"]["GimbalYawDegree"] = $metadata["GimbalYawDegree"] ?? null;
        $data["camera"]["GimbalPitchDegree"] = $metadata["GimbalPitchDegree"] ?? null;
        $data["camera"]["Make"] = null;
        $data["camera"]["Model"] = null;
        $data["camera"]["Software"] = null;
        $data["camera"]["DateTime"] = null;

        $data["camera_pose"]["w2c"] = $metadata["w2c"] ?? [];
        $data["camera_pose"]["tvec"] = $metadata["tvec"] ?? [];
        $data["camera_pose"]["qvec"] = $metadata["qvec"] ?? [];
        $data["camera_pose"]["intrinsic_mtx"] = $metadata["intrinsic_mtx"] ?? [];
        $data["camera_pose"]["cent_point"] = $metadata["cam_cent"] ?? [];
        $data["camera_pose"]["euler_angle"] = $metadata["euler_angle"] ?? [];

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

        $data["exifData"] = $exifData;
        return $data;
    }

}
