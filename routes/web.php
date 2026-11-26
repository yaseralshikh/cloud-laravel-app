<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('users', function () {
        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    })->name('users.index');
});

require __DIR__.'/settings.php';
