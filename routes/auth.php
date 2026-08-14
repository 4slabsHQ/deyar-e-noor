<?php

use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
| Fortify registers login, register, logout, password reset, email verification,
| and password confirmation routes. Only keep authenticated routes Fortify does not provide.
*/

Route::middleware('auth')->group(function () {
    Route::put('user/password', [PasswordController::class, 'update'])->name('user-password.update');
});
