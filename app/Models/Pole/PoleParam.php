<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $pole_id)
 * @method static find(mixed $pole_param_id)
 * @method static findOrfail(mixed $pole_param_id)
 * @property mixed $updated_at
 */
class PoleParam extends Model
{
    protected $table = 'pole_params';
    protected $fillable = ['pole_id', 'height', 'is_roof', 'house_height', 'diameter_top_tube', 'diameter_bottom_tube', 'diameter_strut_tube', 'diameter_body_tube',
        'tilt_angle', 'is_shielded', 'size', 'top_size', 'foot_size', 'description', 'is_active', 'user_id'];
    protected $hidden = ['created_at', 'updated_at'];
    public $appends = ['date_time'];
    public function getDateTimeAttribute()
    {
        return $this->updated_at->format('Y-m-d H:i:s');
    }
}
