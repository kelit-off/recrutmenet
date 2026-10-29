<?php

use App\Http\Controllers\Auth\DevLoginController;
use App\Http\Controllers\Auth\DiscordAuthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/connexion', 'auth.login')->name('login');
    Route::get('/auth/discord/redirect', [DiscordAuthController::class, 'redirect'])->name('auth.discord.redirect');
    Route::get('/auth/discord/callback', [DiscordAuthController::class, 'callback'])->name('auth.discord.callback');
});

Route::post('/deconnexion', [DiscordAuthController::class, 'logout'])->middleware('auth')->name('logout');

if (app()->environment('local')) {
    Route::get('/dev/login/{user}', DevLoginController::class)->name('dev.login');
    Route::get('/dev/users', fn () => view('auth.dev-users', ['users' => User::with('roles')->orderBy('id')->get()]))->name('dev.users');
}
