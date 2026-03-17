<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedSeat extends Model
{
    protected $table = "reserved_seats";

    protected $fillable = [
        "seat_number"
    ];
}
