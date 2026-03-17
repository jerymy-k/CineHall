<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = "movie_sessions";

    protected $fillable = [
        "start_at",
        "end_at",
        "language",
        "price"
    ];
}
