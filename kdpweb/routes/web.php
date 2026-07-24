<?php

use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SetupController;
use App\Models\Classroom;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Setup Route (initialize database and create admin user)
Route::get('/setup', [SetupController::class, 'initialize']);

// Registration Routes
Route::get('/register', function () {
    return redirect()->route('login');
})->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Login Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::resource('departments', DepartmentController::class)->only(['index', 'store']);
    Route::resource('faculties', FacultyController::class);
    Route::resource('classrooms', ClassroomController::class);
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');

    Route::get('/dashboard', function () {
        $stats = [
            'students' => User::count(),
            'faculty' => Faculty::count(),
            'departments' => Department::count(),
            'classes' => Classroom::where('availability', true)->count(),
            'attendance' => 0,
            'notifications' => Notification::count(),
        ];

        return view('dashboard', compact('stats'));
    })->name('dashboard');

    Route::get('/reports', function () {
        return view('reports.index');
    })->name('reports.index');
});

// Public Routes
Route::get('/', function () {
    return view('welcome');
});
