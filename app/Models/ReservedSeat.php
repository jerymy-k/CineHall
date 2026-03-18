<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservedSeat extends Model
{
    use HasFactory;
    protected $table = "reserved_seats";

    protected $fillable = [
        "seat_number"
    ];

    public function reservation () : BelongsTo {
        return $this->belongsTo(Reservation::class);
    }
}
