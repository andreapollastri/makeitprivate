<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RepositoriesController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/repo/delete/{id}', [RepositoriesController::class, 'delete']);
Route::middleware(['auth:sanctum', 'verified'])->post('/repo/create', [RepositoriesController::class, 'create']);
Route::any('/repo/update/{token}/{id}', [RepositoriesController::class, 'update']);
Route::get('/packages.json', [RepositoriesController::class, 'show']);
