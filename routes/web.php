<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\LeaveController;
use Illuminate\Support\Facades\Route;

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

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Events
    Route::resource('events', EventController::class);
    Route::get('/events-calendar', [EventController::class, 'calendar'])->name('events.calendar');
    Route::put('/events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
    Route::put('/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    Route::post('/events/{event}/assign', [EventController::class, 'assign'])->name('events.assign');

    // Alerts
    Route::resource('alerts', AlertController::class);
    Route::post('/alerts/{alert}/send', [AlertController::class, 'send'])->name('alerts.send');
    Route::post('/alerts/{alert}/cancel', [AlertController::class, 'cancel'])->name('alerts.cancel');
    Route::get('/alerts-logs', [AlertController::class, 'logs'])->name('alerts.logs');
    Route::get('/alerts/recipients/{event}', [AlertController::class, 'getRecipients'])->name('alerts.recipients');

    // Users (Manager only)
    Route::middleware('App\Http\Middleware\CheckRole:manager')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Roles (Manager only)
    Route::middleware('App\Http\Middleware\CheckRole:manager')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.avatar');
    Route::post('/profile/skills', [ProfileController::class, 'addSkill'])->name('profile.skills.add');
    Route::delete('/profile/skills/{skill}', [ProfileController::class, 'removeSkill'])->name('profile.skills.remove');
    Route::post('/profile/experiences', [ProfileController::class, 'addExperience'])->name('profile.experiences.add');
    Route::delete('/profile/experiences/{experience}', [ProfileController::class, 'removeExperience'])->name('profile.experiences.remove');
    Route::post('/profile/documents', [ProfileController::class, 'uploadDocument'])->name('profile.documents.upload');
    Route::get('/profile/documents/{document}/download', [ProfileController::class, 'downloadDocument'])->name('profile.documents.download');
    Route::delete('/profile/documents/{document}', [ProfileController::class, 'deleteDocument'])->name('profile.documents.delete');

    // Team
    Route::get('/team', function() { return redirect()->route('team.groups'); })->name('team.index');
    Route::get('/team/create', function() { return redirect()->route('team.groups.create'); })->name('team.create');
    Route::get('/team/orgchart', [TeamController::class, 'orgchart'])->name('team.orgchart');
    Route::get('/team/groups', [TeamController::class, 'groups'])->name('team.groups');
    Route::get('/team/groups/create', [TeamController::class, 'createGroup'])->name('team.groups.create');
    Route::post('/team/groups', [TeamController::class, 'storeGroup'])->name('team.groups.store');
    Route::get('/team/groups/{teamGroup}/edit', [TeamController::class, 'editGroup'])->name('team.groups.edit');
    Route::put('/team/groups/{teamGroup}', [TeamController::class, 'updateGroup'])->name('team.groups.update');
    Route::delete('/team/groups/{teamGroup}', [TeamController::class, 'destroyGroup'])->name('team.groups.destroy');
    Route::post('/team/groups/{teamGroup}/members', [TeamController::class, 'addMember'])->name('team.groups.members.add');
    Route::delete('/team/groups/{teamGroup}/members/{user}', [TeamController::class, 'removeMember'])->name('team.groups.members.remove');

    // Leaves
    Route::resource('leaves', LeaveController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/leaves/{leave}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');
    Route::post('/leaves/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leaves.cancel');
});
