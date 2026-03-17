<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservation extends Model
{
    protected $table = "reservations";

    protected $fillable = [
        "status",
        "total_price"
    ];

    public function user () : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function session () : BelongsTo {
        return $this->belongsTo(Session::class);
    }

    public function reserved_seats () : HasMany {
        return $this->hasMany(ReservedSeat::class);
    }
}
