<?php

namespace App\Models\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $all)
 * @method static findOrFail($id)
 */
class Country extends Model
{
    protected $table = 'countries';
    protected $fillable = ['name', 'code', 'phone_code', 'currency', 'language'];
    protected $hidden = ['created_at', 'updated_at'];
}
