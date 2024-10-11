<?php

namespace App\Models\Geometry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class GeometryCone extends Model
{
    protected $table = 'geometry_cones';
    protected $fillable = [
        'radius',
        'height',
        'radial_segments',
        'pos_x',
        'pos_y',
        'pos_z',
        'rotate_x',
        'rotate_y',
        'rotate_z',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
