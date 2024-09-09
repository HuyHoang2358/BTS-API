<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 */
class Location extends Model
{
    protected $table = 'locations';
    protected $fillable = ['latitude', 'longitude', 'height'];
    protected $hidden = ['created_at', 'updated_at'];
}
