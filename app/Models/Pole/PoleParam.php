<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $array)
 */
class PoleParam extends Model
{
    protected $table = 'pole_params';
    protected $fillable = ['pole_id', 'key', 'value'];
    protected $hidden = ['created_at', 'updated_at'];
}
