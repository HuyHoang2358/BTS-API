<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\DataProcessing;
use Illuminate\Http\Request;

class DataFlowController extends Controller
{
    public function init($stationCode): string
    {
        //TODO: copy station data to new or create new station data if not exist

        return "Create done";
    }
    public function imageProcessing(): array
    {
        $imageFolder = 'E:/Viettel/Projects/BTS/data/bts/images/HAN0212/';
        $imageFilenames = DataProcessing::getImages($imageFolder);
        $data['imageFilenames'] = $imageFilenames;

        foreach ($imageFilenames as $imageFilename) {
            $src_path = $imageFolder . $imageFilename;
            $dst_path = $imageFolder . 'thumb/' . $imageFilename;
            DataProcessing::resizeImage($src_path, $dst_path);
            $data['metadata'] = DataProcessing::getMetadata($src_path);
            //TODO: save image information to database
            break;
        }

        return $data;
    }

    public function convertModel3D($modelPath)
    {
        // save model information to database
        $data['modelPath'] = $modelPath;
        return $data;
    }

    public function extractInfomation($modelPath)
    {
        // save model information to database
        $data['extractInfomation'] = $modelPath;
        return $data;
    }


    public function index(){
        $stationCode = 'HAN-0212';
        $data["init"] = $this->init($stationCode);
        $data["imageProcessing"] = $this->imageProcessing();
        $data["convertModel3D"] = $this->convertModel3D('');
        $data["extractInformation"] = $this->extractInfomation('');
        $data["done"] = "Done";

        return ApiResponse::success($data);
    }
}
