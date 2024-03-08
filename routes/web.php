<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [EventController::class, 'cartelera'])->name('eventos.cartelera');

Route::get('/calendario', [EventController::class, 'calendario'])->name('eventos.calendario');

Route::get('/creditos', [EventController::class, 'creditos'])->name('desarrolladores');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('dashboard',[UserController::class,'dashboard'])->middleware(['auth','verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// rutas adicionales
require __DIR__.'/routes/users.php';
require __DIR__.'/routes/departments.php';
require __DIR__.'/routes/spaces.php';
require __DIR__.'/routes/events.php';
require __DIR__.'/routes/event-schedule.php';
require __DIR__.'/routes/roles.php';
require __DIR__.'/routes/permissions.php';
require __DIR__.'/routes/event-type.php';
require __DIR__.'/routes/event-participant.php';
require __DIR__.'/routes/event-spaces.php';
require __DIR__.'/routes/resources.php';
