<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = "reservations";

    protected $fillable = [
        "status",
        "total_price"
    ];
}
