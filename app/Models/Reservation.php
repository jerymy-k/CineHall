<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    protected $table = "reservations";

    protected $fillable = [
        "status",
        "total_price",
        "expires_at"
    ];

    /**
     * Scope for expired pending reservations
     */
    public function scopeExpiredPending($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '<', now());
    }

    /**
     * Scope for valid reservations
     */
    public function scopeValid($query)
    {
        return $query->where(function ($q) {
            $q->where('status', '!=', 'canceled')
              ->orWhereNull('expires_at');
        })->orWhere('status', 'paid');
    }

    public function user () : BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function session () : BelongsTo {
        return $this->belongsTo(Session::class);
    }

    public function reserved_seats () : HasMany {
        return $this->hasMany(ReservedSeat::class);
    }

    public function payment () : HasOne {
        return $this->hasOne(Payment::class);
    }
}
