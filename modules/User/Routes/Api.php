<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/register', [UserController::class,'store'])->name('register');

Route::middleware('auth:sanctum')->group(function () {
     Route::get('/users', [UserController::class,'getAllUsers'])->name('');
});