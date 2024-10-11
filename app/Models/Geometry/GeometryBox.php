<?php

namespace App\Models\Geometry;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(int[] $array)
 */
class GeometryBox extends Model
{
    protected $table = 'geometry_boxes';
    protected $fillable = ['depth', 'width', 'height', 'pos_x', 'pos_y', 'pos_z', 'rotate_x', 'rotate_y', 'rotate_z'];
    protected $hidden = ['created_at', 'updated_at'];
}
