<?php

namespace App\Models\Address;

use App\Models\WindyArea;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 */
class Commune extends Model
{
    protected $table = 'communes';
    protected $fillable = ['name', 'code', 'slug', 'district_id', 'windy_area_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function windyArea(): BelongsTo
    {
        return $this->belongsTo(WindyArea::class);
    }
}
