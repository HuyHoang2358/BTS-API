<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 * @method static where(string $string, $id)
 */
class StationPole extends Model
{
    protected $table = 'station_pole';
    protected $fillable = ['station_code', 'pole_id', 'built_on'];
    protected $hidden = ['created_at', 'updated_at'];

}
