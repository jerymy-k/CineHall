<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $table = "rooms";

    protected $fillable = [
        "name",
        "total_seats",
        "is_vip"
    ];

    public function sessions () : HasMany {
        return $this->hasMany(Session::class);
    }
}
