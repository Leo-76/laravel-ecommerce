<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// ── Accueil ───────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class, 'showForm'])->name('login');
    Route::post('/login',   [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── Dashboard ─────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ── Admin ─────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',                                [AdminController::class, 'index'])->name('index');
    Route::get('/utilisateurs',                    [AdminController::class, 'utilisateurs'])->name('utilisateurs');
    Route::patch('/utilisateurs/{user}/role',      [AdminController::class, 'changerRole'])->name('role');
    Route::patch('/utilisateurs/{user}/ecommerce', [AdminController::class, 'toggleEcommerce'])->name('ecommerce-toggle');
});
