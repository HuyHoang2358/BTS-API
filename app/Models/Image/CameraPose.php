<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraPose extends Model
{
    protected $table = 'camera_poses';
    protected $fillable = [
        'image_id',
        'w2c',
        'tvec',
        'qvec',
        'cent_point',
        'euler_angle',
        'intrinsic_mtx',
    ];
}
