<?php

namespace App\Models\Device;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 * @method static select(string $string, string $string1, string $string2, string $string3, string $string4)
 * @method static where(string $string, mixed $vendor)
 */
class Vendor extends Model
{
    protected  $table = 'vendors';
    protected $fillable = ['name', 'slug', 'description', 'website', 'logo'];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
