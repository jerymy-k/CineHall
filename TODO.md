# CineHall Room/Seat/Reservation Features TODO

## Current Progress: 0/18 [ ]

### 1. Database Migrations (4 steps)
- [x] Create migration: add_type_to_movie_sessions_table
- [x] Create migration: add_expires_at_to_reservations_table
- [x] Run `php artisan migrate`
- [x] Verify schema (check DB)

### 2. Model Updates (3 steps)
 - [x] Edit app/Models/Session.php (add type fillable/relations)
 - [x] Edit app/Models/Reservation.php (add expires_at, scopes)
 - [x] Edit app/Models/User.php if needed (minor)

### 3. Controllers Creation (4 steps)
- [x] Create app/Http/Controllers/Api/RoomController.php (CRUD)
- [x] Create app/Http/Controllers/Api/SessionController.php (CRUD + availableSeats)
- [x] Create app/Http/Controllers/Api/ReservationController.php (CRUD + logic)
- [x] Edit app/Http/Controllers/PaimentController.php (handle status update)

### 4. Routes (1 step)
- [x] Edit routes/api.php (add all new routes)

### 5. Queue Job (2 steps)
- [x] Create app/Jobs/CleanupExpiredReservations.php
- [x] Schedule in app/Providers/AppServiceProvider.php or kernel

### 6. Factories/Seeders (2 steps)
- [x] Update factories (SessionFactory, ReservationFactory for type/expires_at)
- [x] Update seeders + run `php artisan db:seed --class=RoomSeeder` etc.

### 7. Testing/Setup (2 steps)
- [ ] Setup queue: `php artisan queue:table && migrate && queue:work`
- [ ] Test APIs (list key endpoints to test)

**Next:** Complete migrations first.

