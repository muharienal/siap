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
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update.password');
    Route::post('/profile/remove-photo', [App\Http\Controllers\ProfileController::class, 'removePhoto'])->name('profile.remove.photo');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Ruang Meeting (halaman publik untuk karyawan lihat & pilih ruangan)
    Route::get('/rooms', [RoomController::class, 'list'])->name('rooms.list');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');

    // ========== SETTINGS MANUAL ROUTES ==========
    // Ruangan
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

        // Fasilitas
        Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
        Route::get('/facilities/create', [FacilityController::class, 'create'])->name('facilities.create');
        Route::post('/facilities', [FacilityController::class, 'store'])->name('facilities.store');
        Route::get('/facilities/{facility}/edit', [FacilityController::class, 'edit'])->name('facilities.edit');
        Route::put('/facilities/{facility}', [FacilityController::class, 'update'])->name('facilities.update');
        Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy'])->name('facilities.destroy');

        // Divisi
        Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
        Route::get('/divisions/create', [DivisionController::class, 'create'])->name('divisions.create');
        Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
        Route::get('/divisions/{division}/edit', [DivisionController::class, 'edit'])->name('divisions.edit');
        Route::put('/divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
        Route::delete('/divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

        // Jabatan
        Route::get('/positions', [PositionController::class, 'index'])->name('positions.index');
        Route::get('/positions/create', [PositionController::class, 'create'])->name('positions.create');
        Route::post('/positions', [PositionController::class, 'store'])->name('positions.store');
        Route::get('/positions/{position}/edit', [PositionController::class, 'edit'])->name('positions.edit');
        Route::put('/positions/{position}', [PositionController::class, 'update'])->name('positions.update');
        Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ========== END SETTINGS ==========

    // Settings - Users (manajemen pengguna + karyawan)
    Route::prefix('settings')->name('settings.')->group(function () {
        // ... routes lain
        Route::resource('users', App\Http\Controllers\UserController::class);
    });

    // Complaints
    Route::resource('complaints', App\Http\Controllers\ComplaintController::class);
    Route::post('complaints/{complaint}/respond', [App\Http\Controllers\ComplaintController::class, 'respond'])->name('complaints.respond');

    // Booking routes
    Route::resource('bookings', App\Http\Controllers\BookingController::class);
    Route::post('bookings/check-availability', [App\Http\Controllers\BookingController::class, 'checkAvailability'])->name('bookings.check-availability');
    Route::get('bookings/room-schedule', [App\Http\Controllers\BookingController::class, 'roomDaySchedule'])->name('bookings.room-schedule');
    Route::patch('bookings/{booking}/approve', [App\Http\Controllers\BookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('bookings/{booking}/reject', [App\Http\Controllers\BookingController::class, 'reject'])->name('bookings.reject');
    Route::post('bookings/bulk-action', [App\Http\Controllers\BookingController::class, 'bulkAction'])->name('bookings.bulk-action');
    Route::get('bookings/export', [App\Http\Controllers\BookingController::class, 'export'])->name('bookings.export');
    Route::get('bookings/{booking}/qr-code', [App\Http\Controllers\BookingController::class, 'showQrCode'])->name('bookings.qr-code');

    // Notification routes
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/get', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

});

// Attendance routes (accessible without auth for guest attendance)
Route::get('booking/meet/{code}', [App\Http\Controllers\BookingController::class, 'attendMeeting'])->name('attendance.meet');
Route::post('booking/meet/{code}', [App\Http\Controllers\BookingController::class, 'submitAttendance'])->name('attendance.submit');
Route::get('attendance/success/{code}', function($code) {
    return view('attendance.success', ['code' => $code]);
})->name('attendance.success');

// Settings index redirect (fix for missing route)
Route::get('/settings', function () {
    return redirect('/settings/rooms');
})->name('settings.index');

Route::delete('settings/rooms/{room}/delete-photo', [RoomController::class, 'deletePhoto'])->name('settings.rooms.delete-photo');