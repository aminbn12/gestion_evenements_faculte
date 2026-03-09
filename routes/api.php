<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Events for FullCalendar
    Route::get('/events', [EventController::class, 'calendar'])->name('events.calendar');

    // Notifications polling
    Route::get('/notifications', function () {
        return auth()->user()->unreadNotifications;
    });

    // Mark notification as read
    Route::post('/notifications/{id}/read', function ($id) {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    });

    // Members for Select2
    Route::get('/members', function () {
        return \App\Models\User::where('status', 'active')
            ->select('id', 'first_name', 'last_name', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => $user->full_name . ' (' . $user->email . ')',
                ];
            });
    });
});
