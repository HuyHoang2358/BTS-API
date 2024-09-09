<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 */
class Pole extends Model
{
    protected $table = 'poles';

    protected $fillable = [
        'name',
        'height',
        'is_roof',
        'house_height',
        'pole_category_id',
        'size',
        'diameter_body_tube',
        'structure',
        'description'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PoleCategory::class, 'pole_category_id');
    }
}
