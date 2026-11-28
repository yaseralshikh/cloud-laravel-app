<?php

use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('users', UsersIndex::class)->name('users.index');
});

Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
    Route::get('users', UsersIndex::class)->name('users.index');
});

Route::post('/language/{locale}', function (Request $request, string $locale) {
    $request->session()->put('locale', $locale);

    return back();
})->whereIn('locale', ['ar', 'en'])->name('language.update');

require __DIR__.'/settings.php';
