<?php

namespace App\Models\Image;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsLocations extends Model
{
    protected $table = 'gps_locations';
    protected $fillable = [
        'image_id',
        'latitude',
        'longitude',
        'altitude',
        'latitude_ref',
        'longitude_ref',
        'altitude_ref',
    ];

}
