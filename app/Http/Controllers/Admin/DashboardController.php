<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Session;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{

    #[OA\Get(
        path: "/admin/dashboard",
        summary: "Admin dashboard stats",
        security: [["bearerAuth" => []]],
        tags: ["Admin"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Dashboard data"
            )
        ]
    )]
    
    public function index()
    {
        return response()->json([
            'overview' => $this->overview(),
            'revenue' => $this->revenue(),
            'occupancy_rate' => $this->occupancyRate(),
            'popular_movies' => $this->popularMovies(),
            'revenue_per_movie' => $this->revenuePerMovie(),
            'users' => $this->usersStats(),
        ]);
    }

    private function overview()
    {
        return [
            'movies' => Movie::count(),
            'sessions' => Session::count(),
            'reservations' => Reservation::count(),
            'tickets' => DB::table('reserved_seats')->count(),
        ];
    }

    private function revenue()
    {
        return [
            'total' => DB::table('reservations')
                ->where('status', 'paid')
                ->sum('total_price')
        ];
    }

    private function occupancyRate()
    {
        $totalSeats = DB::table('movie_sessions')
            ->join('rooms', 'movie_sessions.room_id', '=', 'rooms.id')
            ->sum('rooms.total_seats');

        $occupiedSeats = DB::table('reserved_seats')->count();

        return $totalSeats > 0
            ? round(($occupiedSeats / $totalSeats) * 100, 2)
            : 0;
    }

    private function popularMovies()
    {
        return DB::table('reserved_seats')
            ->join('reservations', 'reserved_seats.reservation_id', '=', 'reservations.id')
            ->join('movie_sessions', 'reservations.session_id', '=', 'movie_sessions.id')
            ->join('movies', 'movie_sessions.movie_id', '=', 'movies.id')
            ->select('movies.title', DB::raw('COUNT(reserved_seats.id) as tickets_sold'))
            ->groupBy('movies.title')
            ->orderByDesc('tickets_sold')
            ->limit(5)
            ->get();
    }

    private function revenuePerMovie()
    {
        return DB::table('reservations')
            ->join('movie_sessions', 'reservations.session_id', '=', 'movie_sessions.id')
            ->join('movies', 'movie_sessions.movie_id', '=', 'movies.id')
            ->where('reservations.status', 'paid')
            ->select('movies.title', DB::raw('SUM(reservations.total_price) as revenue'))
            ->groupBy('movies.title')
            ->orderByDesc('revenue')
            ->get();
    }

    private function usersStats()
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::where('is_admin', true)->count(),
        ];
    }
}
