<?php

use App\Http\Controllers\EventSpaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['role:Coordinador|Gestor de espacios'])->group(function () {
    // Ruta para agregar un participante al evento
    Route::get('/solicitud_de_espacios', [EventSpaceController::class, 'index'])->name('event_spaces.review');
    Route::get('/solicitud_de_espacios/filter', [EventSpaceController::class, 'indexFilter'])->name('event_spaces.review.filter');
    Route::post('/solicitud_de_espacios/filter', [EventSpaceController::class, 'indexFilter'])->name('event_spaces.review.filter');
    Route::get('/solicitud_de_espacios/pendientes', [EventSpaceController::class, 'awaitingRequests'])->name('event_spaces.awaitingRequests');

    Route::get('/evento/autorizar/{event}', [EventSpaceController::class, 'authorizeRequestSpace'])->name('eventspace.authorize');
    Route::get('/evento/motivo/{event}',[EventSpaceController::class,'preRejectRequestSpace'])->name('eventspace.preReject');
    Route::post('/evento/rechazar', [EventSpaceController::class, 'rejectRequestSpace'])->name('eventspace.reject');
    
});