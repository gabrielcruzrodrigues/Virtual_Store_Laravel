<?php

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\UserController;

Route::resource('/users', UserController::class)
     ->only(['index', 'store']);

Route::post('/login', [UserController::class, 'login']);