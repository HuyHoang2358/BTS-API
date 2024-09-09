<?php

namespace App\Models\Pole;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoleDevice extends Model
{
   protected $table =   'pole_device';
   protected $fillable = [
       'pole_id',
       'device_id',
       'installed_at',
       'x',
       'y',
       'z',
       'anpha',
       'beta',
       'gama',
   ];

}
