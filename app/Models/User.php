<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
// use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable
 {
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable; // Billable temporarily disabled for seeding



    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'is_admin',
        'is_active',
    ];

    protected $hidden = [
        'password'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function reservations () : HasMany {
        return $this->hasMany(Reservation::class);
    }
}
