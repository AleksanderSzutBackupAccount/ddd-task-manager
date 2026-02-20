<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Src\Identity\Infrastructure\Laravel\Http\GetUsersController;
use Src\Identity\Infrastructure\Laravel\Http\LoginController;
use Src\Identity\Infrastructure\Laravel\Http\MeController;
use Src\Project\Infrastructure\Laravel\Http\Controllers\CreateProjectController;
use Src\Project\Infrastructure\Laravel\Http\Controllers\GetMembersController;
use Src\Project\Infrastructure\Laravel\Http\Controllers\GetProjectsController;

Route::post('/auth/login', LoginController::class);

Route::group(['middleware' => ['auth.jwt']], static function () {
    Route::get('/users', GetUsersController::class);

    Route::get('/auth/me', MeController::class);

    Route::prefix('projects')->group(function () {
        Route::post('/', CreateProjectController::class);
        Route::get('/', GetProjectsController::class);

        Route::prefix('{projectId}')->group(function () {
            Route::get('/members', GetMembersController::class);
        });
    });
});
