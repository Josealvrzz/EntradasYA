<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CrudTestController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\EntradaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('crud-test', CrudTestController::class);
Route::resource('eventos', EventoController::class);
Route::resource('lugares', LugarController::class);
Route::resource('entradas', EntradaController::class);

require __DIR__.'/auth.php';
