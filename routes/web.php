<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\TaskController;
use App\Http\Controllers\Web\ProjectController;
use App\Http\Controllers\Web\CompanyController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\LanguageController;
use App\Http\Controllers\Web\CompanyJoinRequestController;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'changeStatus'])->name('tasks.status');
    
    // Projects
    Route::resource('projects', ProjectController::class);
    
    // Companies
    Route::resource('companies', CompanyController::class);
    
    // Company Join Requests
    Route::get('/join-requests', [CompanyJoinRequestController::class, 'index'])->name('join-requests.index');

    // Users
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])
        ->name('users.toggle-active');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Language switch (available for both guests and authenticated users)
Route::get('/language/{locale}', [LanguageController::class, 'change'])->name('language.change');

// Fallback route
Route::fallback(function () {
    return redirect()->route('dashboard');
});
