<?php

use Illuminate\Support\Facades\Route;
use Src\Identity\Infrastructure\Laravel\Http\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/login', LoginController::class);
