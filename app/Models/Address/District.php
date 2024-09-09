<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 * @method static updateOrCreate(array $array, array $array1)
 */
class District extends Model
{
    protected $table = 'districts';
    protected $fillable = ['name', 'code', 'slug', 'province_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
