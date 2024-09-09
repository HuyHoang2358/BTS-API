<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Location;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(mixed $validated)
 * @method static findOrFail($id)
 */
class Station extends Model
{
    protected $table = 'stations';
    protected $fillable = ['name', 'code', 'description', 'location_id', 'address_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
