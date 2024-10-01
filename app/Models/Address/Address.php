<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @method static create(array $array)
 * @property mixed $commune
 * @property mixed $detail
 * @property mixed $district
 * @property mixed $province
 * @property mixed $country
 */
class Address extends Model
{
    protected $table = 'addresses';
    protected $fillable = ['detail', 'country_id', 'province_id', 'district_id', 'commune_id'];
    protected $hidden = ['created_at', 'updated_at', 'country_id', 'province_id', 'district_id', 'commune_id'];
    protected $appends = ['address_detail'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }
    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class, 'commune_id');
    }

    public function getAddressDetailAttribute(): string
    {
        return ($this->detail ? $this->detail. ', ' : '' ).
            ($this->commune ?  $this->commune->name . ', ' : '').
            ($this->district ? $this->district->name . ', ' : '').
            ($this->province ? $this->province->name . ', ' : '').
            ($this->country ? $this->country->name : '');
    }
}
