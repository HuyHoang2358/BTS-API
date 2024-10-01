<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find($pole_device_id)
 * @method static create(array $array)
 * @method static findOrFail($id)
 * @method static where(string $string, $pole_id)
 */
class PoleDevice extends Model
{
   protected $table = 'pole_device';
   protected $fillable = [
       'pole_id',
       'device_id',
       'attached_at',
       'x',
       'y',
       'z',
       'alpha',
       'beta',
       'gama',
       'tilt',
       'azimuth',
       'height',
       'vertices',
       'translation',
       'rotation',
       'suggested_devices',
       'suggested_img',
       'description'
   ];

}
