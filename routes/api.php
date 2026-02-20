<?php

use Illuminate\Support\Facades\Route;
use Src\Identity\Infrastructure\Laravel\Http\LoginController;
use Src\Identity\Infrastructure\Laravel\Http\MeController;
use Src\Project\Infrastructure\Laravel\Http\CreateProjectController;
use Src\Project\Infrastructure\Laravel\Http\GetProjectsController;

Route::post('/auth/login', LoginController::class);

Route::group(['middleware' => ['auth.jwt']], static function () {
    Route::get('/auth/me', MeController::class);

    Route::prefix('projects')->group(function () {
        Route::post('/', CreateProjectController::class);
        Route::get('/', GetProjectsController::class);
    });
});
