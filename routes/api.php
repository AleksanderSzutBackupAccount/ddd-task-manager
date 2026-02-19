<?php

use Illuminate\Support\Facades\Route;
use Src\Identity\Infrastructure\Laravel\Http\LoginController;
use Src\Identity\Infrastructure\Laravel\Http\MeController;

Route::post('/auth/login', LoginController::class);
Route::get('/auth/me', MeController::class)->middleware('auth.jwt');
