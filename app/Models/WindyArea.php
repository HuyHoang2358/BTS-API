<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 * @method static where(string $string, mixed $vungGio)
 */
class WindyArea extends Model
{
    protected $table = 'windy_areas';
    protected $fillable = [
        'name',
        'wo',
        'v3s50',
        'v10m50',
        'description',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
