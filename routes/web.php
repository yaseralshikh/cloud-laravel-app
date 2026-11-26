<?php

use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('users', UsersIndex::class)->name('users.index');
});

Route::middleware('web')->get('/debug-roles', fn () => [
    'logged_in'  => auth()->check(),
    'id'         => auth()->id(),
    'email'      => auth()->user()?->email,
    'roles'      => auth()->user()?->roles->pluck('name'),
    'superadmin' => auth()->user()?->hasRole('superadmin'),
]);

require __DIR__.'/settings.php';
