<?php

use Illuminate\Support\Facades\Route;

// Halaman publik uji (tanpa login).
Route::view('/', 'publik.terjemah')->name('terjemah');
Route::view('kamus', 'publik.kamus')->name('kamus');
Route::view('donasi', 'publik.donasi')->name('donasi');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Panel moderasi koreksi — hanya admin/moderator.
Route::view('moderasi', 'koreksi.moderasi')
    ->middleware(['auth', 'verified', 'can-moderate'])
    ->name('moderasi');

// Kelola pengguna & tunjuk moderator — hanya admin.
Route::view('pengguna', 'pengguna.kelola')
    ->middleware(['auth', 'verified', 'can-admin'])
    ->name('pengguna');

require __DIR__ . '/auth.php';
