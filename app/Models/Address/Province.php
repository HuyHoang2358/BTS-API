<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 */
class Province extends Model
{
    protected $table = 'provinces';
    protected $fillable = ['name', 'code', 'slug', 'country_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

}
