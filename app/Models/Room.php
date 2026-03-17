<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = "rooms";

    protected $fillable = [
        "name",
        "total_seats",
        "is_vip"
    ];
}
