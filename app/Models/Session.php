<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Session extends Model
{
    protected $table = "movie_sessions";

    protected $fillable = [
        "start_at",
        "end_at",
        "language",
        "price"
    ];

    public function room () : BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function movie () : BelongsTo {
        return $this->BelongsTo(Movie::class);
    }

    public function reservations () : HasMany {
        return $this->hasMany(Reservation::class);
    }
}
