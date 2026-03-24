<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineHall E-Ticket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: #f4f4f4;
            color: #1a1a2e;
        }

        .ticket {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 2px solid #e94560;
            border-radius: 12px;
            overflow: hidden;
        }

        /* Header */
        .ticket-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: #ffffff;
            padding: 24px 30px;
            text-align: center;
        }

        .ticket-header h1 {
            font-size: 28px;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 4px;
            color: #e94560;
        }

        .ticket-header .subtitle {
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #a8a8b3;
        }

        /* Movie Title Banner */
        .movie-banner {
            background: #e94560;
            color: #ffffff;
            padding: 16px 30px;
            text-align: center;
        }

        .movie-banner h2 {
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Body */
        .ticket-body {
            padding: 24px 30px;
        }

        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }

        .info-row {
            display: table-row;
        }

        .info-item {
            display: table-cell;
            padding: 8px 4px;
            width: 50%;
            vertical-align: top;
        }

        .info-item .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #888;
            margin-bottom: 3px;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a2e;
        }

        /* Divider */
        .divider {
            border: none;
            border-top: 2px dashed #ddd;
            margin: 16px 0;
        }

        /* Section Title */
        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #e94560;
            margin-bottom: 10px;
            font-weight: bold;
        }

        /* Seats */
        .seats-list {
            margin-bottom: 8px;
        }

        .seat-badge {
            display: inline-block;
            background: #1a1a2e;
            color: #ffffff;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: bold;
            margin: 2px 3px;
        }

        /* QR Section */
        .qr-section {
            text-align: center;
            padding: 20px 30px;
            background: #f9f9fb;
            border-top: 2px dashed #ddd;
        }

        .qr-section .qr-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #888;
            margin-bottom: 10px;
        }

        .qr-code {
            display: inline-block;
        }

        .qr-section .reservation-id {
            font-size: 11px;
            color: #aaa;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        /* Total */
        .total-section {
            background: #1a1a2e;
            color: #ffffff;
            padding: 16px 30px;
            text-align: center;
        }

        .total-section .total-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #a8a8b3;
        }

        .total-section .total-amount {
            font-size: 26px;
            font-weight: bold;
            color: #e94560;
        }

        /* Footer */
        .ticket-footer {
            text-align: center;
            padding: 12px 30px;
            background: #f9f9fb;
            border-top: 1px solid #eee;
        }

        .ticket-footer p {
            font-size: 10px;
            color: #aaa;
            letter-spacing: 0.5px;
        }

        /* VIP Badge */
        .vip-badge {
            display: inline-block;
            background: #ffd700;
            color: #1a1a2e;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        {{-- Header --}}
        <div class="ticket-header">
            <h1>🎬 CineHall</h1>
            <div class="subtitle">E-Ticket — Admit {{ $reservation->reserved_seats->count() }}</div>
        </div>

        {{-- Movie Banner --}}
        <div class="movie-banner">
            <h2>{{ $reservation->session->movie->title ?? 'N/A' }}</h2>
        </div>

        {{-- Body --}}
        <div class="ticket-body">
            {{-- Session Details --}}
            <div class="section-title">Session Details</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-item">
                        <div class="label">Date & Start</div>
                        <div class="value">{{ \Carbon\Carbon::parse($reservation->session->start_at)->format('M d, Y — H:i') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">End</div>
                        <div class="value">{{ \Carbon\Carbon::parse($reservation->session->end_at)->format('H:i') }}</div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-item">
                        <div class="label">Language</div>
                        <div class="value">{{ ucfirst($reservation->session->language ?? 'N/A') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Duration</div>
                        <div class="value">{{ $reservation->session->movie->duration ?? 'N/A' }} min</div>
                    </div>
                </div>
            </div>

            <hr class="divider">

            {{-- Room --}}
            <div class="section-title">Room</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-item">
                        <div class="label">Room Name</div>
                        <div class="value">
                            {{ $reservation->session->room->name ?? 'N/A' }}
                            @if($reservation->session->room->is_vip ?? false)
                                <span class="vip-badge">VIP</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="label">Min Age</div>
                        <div class="value">{{ $reservation->session->movie->min_age ?? 'All ages' }}+</div>
                    </div>
                </div>
            </div>

            <hr class="divider">

            {{-- Seats --}}
            <div class="section-title">Reserved Seats</div>
            <div class="seats-list">
                @foreach($reservation->reserved_seats as $seat)
                    <span class="seat-badge">Seat {{ $seat->seat_number }}</span>
                @endforeach
            </div>

            <hr class="divider">

            {{-- Client Info --}}
            <div class="section-title">Client</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-item">
                        <div class="label">Name</div>
                        <div class="value">{{ $reservation->user->first_name }} {{ $reservation->user->last_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Email</div>
                        <div class="value">{{ $reservation->user->email }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- QR Code Section --}}
        <div class="qr-section">
            <div class="qr-label">Scan to verify reservation</div>
            <div class="qr-code">
                {!! $qrCode !!}
            </div>
            <div class="reservation-id">Reservation #{{ $reservation->id }}</div>
        </div>

        {{-- Total --}}
        <div class="total-section">
            <div class="total-label">Total Paid</div>
            <div class="total-amount">{{ number_format($reservation->total_price, 2) }} MAD</div>
        </div>

        {{-- Footer --}}
        <div class="ticket-footer">
            <p>This ticket is valid only for the session shown above. Please present this ticket (printed or digital) at the entrance.</p>
            <p style="margin-top: 4px;">Generated on {{ now()->format('M d, Y — H:i') }}</p>
        </div>
    </div>
</body>
</html>
