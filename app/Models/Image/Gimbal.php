<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gimbal extends Model
{
    protected $table = 'gimbals';
    protected $fillable = [
        'image_id',
        'yaw_degree',
        'pitch_degree',
        'roll_degree',
    ];
}
