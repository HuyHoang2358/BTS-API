<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static where(string $string, mixed $param)
 */
class PoleCategory extends Model
{
    protected $table = 'pole_categories';
    protected $fillable = ['name', 'code','description'];
    protected $hidden = ['created_at', 'updated_at'];

    public function poles(): HasMany
    {
        return $this->hasMany(Pole::class);
    }

}
