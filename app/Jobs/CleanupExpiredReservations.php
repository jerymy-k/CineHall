<?php

namespace App\Jobs;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupExpiredReservations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expiredReservations = Reservation::expiredPending()->get();
        foreach ($expiredReservations as $reservation) {
            $reservation->update(['status' => 'canceled']);
        }

        \Log::info('Cleaned up ' . $expiredReservations->count() . ' expired reservations');
    }
}

