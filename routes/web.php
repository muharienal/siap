<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

// auth middleware group
Route::middleware(['auth'])->group(function () {

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update.password');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Ruang Meeting (halaman publik untuk karyawan lihat & pilih ruangan)
    Route::get('/rooms', [RoomController::class, 'list'])->name('rooms.list');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/live-schedule', [RoomController::class, 'liveSchedule'])->name('rooms.live-schedule');

    // ========== SETTINGS MANUAL ROUTES ==========
    // Settings index redirect
    Route::get('/settings', function () {
        return redirect('/settings/rooms');
    })->name('settings.index');

    // Ruangan & Fasilitas
    // FIX (bug keamanan kritis): seluruh grup /settings sekarang wajib
    // lewat middleware 'admin' (role==1) — sebelumnya cuma 'auth' biasa,
    // siapa pun yang login bisa akses Manajemen Ruang Rapat, Fasilitas,
    // Divisi, Bidang, Pengguna, dan Digital Signage config langsung lewat
    // URL, termasuk membuat/menghapus pengguna lain. Lihat penjelasan
    // lengkap di app/Http/Middleware/EnsureUserIsAdmin.php.
    Route::prefix('settings')->name('settings.')->middleware(['admin'])->group(function () {
        // Manajemen Ruangan
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        
        // INI BAGIAN YANG DIPERBAIKI: Menggunakan showSettings, BUKAN show
        Route::get('/rooms/{room}', [RoomController::class, 'showSettings'])->name('rooms.show');
        
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::delete('/rooms/{room}/delete-photo', [RoomController::class, 'deletePhoto'])->name('rooms.delete-photo');

        // Fasilitas (grid ruangan)
        Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

        // Fasilitas per ruangan
        Route::get('/facilities/room/{room}', [FacilityController::class, 'room'])->name('facilities.room');
        Route::post('/facilities/room/{room}', [FacilityController::class, 'attach'])->name('facilities.room.attach');
        Route::put('/facilities/room/{room}/{facility}', [FacilityController::class, 'updateQuantity'])->name('facilities.room.update');
        Route::delete('/facilities/room/{room}/{facility}', [FacilityController::class, 'detach'])->name('facilities.room.detach');

        // Data master fasilitas
        Route::get('/facilities/master', [FacilityController::class, 'master'])->name('facilities.master');
        Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
        Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
        Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');

        // Divisi (dikelola via modal di halaman Pengguna, bukan halaman terpisah)
        Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
        Route::put('/divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
        Route::delete('/divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

        // Bidang (dikelola via modal di halaman Pengguna, bukan halaman terpisah)
        Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
        Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Digital Signage (tampilan TV depan ruangan)
        Route::get('/signage', [App\Http\Controllers\SignageController::class, 'index'])->name('signage.index');
        Route::post('/signage-all', [App\Http\Controllers\SignageController::class, 'saveAll'])->name('signage.save-all');
        Route::post('/signage-all/regenerate', [App\Http\Controllers\SignageController::class, 'regenerateAllToken'])->name('signage.regenerate-all');
        Route::post('/signage/{room}', [App\Http\Controllers\SignageController::class, 'save'])->name('signage.save');
        Route::post('/signage/{room}/regenerate', [App\Http\Controllers\SignageController::class, 'regenerateToken'])->name('signage.regenerate');
    });

    // ========== END SETTINGS ==========

    // Complaints
    Route::resource('complaints', App\Http\Controllers\ComplaintController::class);
    Route::post('complaints/{complaint}/respond', [App\Http\Controllers\ComplaintController::class, 'respond'])->name('complaints.respond');

    // Booking routes
    // FIX: rute literal (export, room-schedule) HARUS didaftarkan SEBELUM
    // Route::resource('bookings', ...) — resource itu otomatis membuat
    // GET /bookings/{booking} (halaman detail) yang sifatnya wildcard, dan
    // Laravel mencocokkan rute berdasarkan urutan pendaftaran. Kalau
    // resource didaftarkan lebih dulu, "export"/"room-schedule" akan
    // "ketangkap" sebagai {booking} (dianggap ID), lalu gagal dicari di
    // database → 404. Ini sebabnya tombol Export selama ini tidak berfungsi.
    Route::post('bookings/check-availability', [App\Http\Controllers\BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
    Route::post('bookings/available-rooms', [App\Http\Controllers\BookingController::class, 'availableRooms'])->name('bookings.available-rooms');
    Route::get('bookings/room-schedule', [App\Http\Controllers\BookingController::class, 'roomDaySchedule'])->name('bookings.room-schedule');
    Route::post('bookings/bulk-action', [App\Http\Controllers\BookingController::class, 'bulkAction'])->name('bookings.bulk-action');
    Route::get('bookings/export', [App\Http\Controllers\BookingController::class, 'export'])->name('bookings.export');

    Route::get('bookings/{booking}/live', [App\Http\Controllers\BookingController::class, 'live'])->name('bookings.live');
    Route::resource('bookings', App\Http\Controllers\BookingController::class);
    Route::patch('bookings/{booking}/approve', [App\Http\Controllers\BookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('bookings/{booking}/reject', [App\Http\Controllers\BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/{booking}/relocate', [App\Http\Controllers\BookingController::class, 'relocate'])->name('bookings.relocate');
    Route::patch('bookings/{booking}/confirm-relocation', [App\Http\Controllers\BookingController::class, 'confirmRelocation'])->name('bookings.confirm-relocation');
    Route::post('bookings/{booking}/decline-relocation', [App\Http\Controllers\BookingController::class, 'declineRelocation'])->name('bookings.decline-relocation');
    Route::get('bookings/{booking}/attendance/export', [App\Http\Controllers\BookingController::class, 'exportAttendance'])->name('bookings.attendance.export');

    // Notification routes
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/get', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

});

// Attendance routes (accessible without auth for guest attendance)
Route::get('booking/meet/{code}', [App\Http\Controllers\BookingController::class, 'attendMeeting'])->name('attendance.meet');
Route::post('booking/meet/{code}', [App\Http\Controllers\BookingController::class, 'submitAttendance'])->name('attendance.submit');
Route::get('attendance/success/{code}', [App\Http\Controllers\BookingController::class, 'attendanceSuccess'])->name('attendance.success');

// Digital Signage — halaman TV publik, tanpa login, diakses lewat token rahasia
Route::get('signage/{token}', [App\Http\Controllers\SignageController::class, 'show'])->name('signage.show');