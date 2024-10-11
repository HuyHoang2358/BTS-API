<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, $id)
 * @method static create(array $array)
 */
class Measurement extends Model
{
    protected $table = 'measurements';
    protected $fillable = ['scan_id', 'measurements', 'user_id', 'is_active'];
    protected $hidden = ['created_at', 'updated_at'];


    public function scan(): BelongsTo
    {
        return $this->belongsTo(Scan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
