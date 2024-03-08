<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;

Route::middleware(['role:Coordinador|Gestor de eventos'])->group(function () {
    // Ruta para encontrar disponibilidad de horario para reservar
    Route::get('/eventos/disponibilidad', [EventController::class, 'availableSearch'])->name('events.availableSearch');

    // Ruta para acceder a la creación de eventos
    //Route::get('/events/seleccionado', [EventController::class,'createWithSpace'])->name('events.createwithSpace');
    Route::get('/events/seleccionado/{space}/{start_date}/{end_date}/{start_time}/{end_time}', [EventController::class,'createWithSpace'])->name('events.createwithSpace');

    // Ruta para guardar el nuevo evento en la base de datos
    Route::post('/eventos/guardar', [EventController::class, 'store'])->name('events.store');

    //Ruta para acceder a la creación de eventos
    Route::get('/evento/nuevo', [EventController::class,'create'])->name('events.create');

    //Ruta para acceder a la creación de eventos
    //Route::post('/evento/nuevo', [EventController::class,'create'])->name('events.create');

    // Ruta para guadar los participantes de un evento
    Route::get('/events/{event}/participantes', [EventController::class, 'registrarParticipantes'])->name('events.participants');

    // Ruta para guadar los participantes de un evento
    Route::get('/events/{event}/participantes/actualizar', [EventController::class, 'actualizarParticipantes'])->name('events.participants.update');

    // Ruta para guardar el nuevo evento en la base de datos
    //Route::post('/eventos/busca/participante', [EventController::class, 'searchparticipant'])->name('event.searchparticipant');

    //Ruta para acceder a la creación de eventos
    Route::get('/evento/registro/{event}', [EventController::class,'register'])->name('events.register');

    //Ruta para acceder a la creación de eventos
    Route::get('/eventos/area', [EventController::class,'by_area'])->name('events.byArea');

    // Eventos del área que se encuentran en estatus de borrador
    Route::get('/eventos/area/borrador',[EventController::class,'by_area_drafts'])->name('events.byArea.drafts');

    // Eventos del área que no están publicados
    Route::get('/eventos/area/sinpublicar',[EventController::class,'by_area_unpublish'])->name('events.byArea.unPublish');

    // Editar evento
    Route::get('/evento/editar/{event}', [EventController::class,'edit'])->name('event.edit');

    // Actualizar evento
    Route::put('/evento/actualizar/{event}', [EventController::class,'update'])->name('event.update');

    // Solicitud para cancelar un evento
    Route::get('/evento/precancelar/{event}', [EventController::class,'preCancel'])->name('event.preCancel');

    // Cancelación de evento
    Route::post('/evento/cancelar/{event}', [EventController::class,'cancel'])->name('event.cancel');

    // Solicitud para eliminar un evento
    Route::get('/evento/pre-eliminar/{event}', [EventController::class,'preEestroy'])->name('event.preDestroy');

    // Eliminar un evento
    Route::delete('/evento/eliminar/{event}', [EventController::class,'destroy'])->name('event.destroy');

    // Ruta para publicar un evento
    Route::put('/events/{id}/publicar', [EventController::class, 'publish'])->name('events.publish');

    // Ruta para seleccionar los recursos para el evento
    Route::get('/events/{event}/recursos', [EventController::class, 'selectResources'])->name('event.selectResources');

    // Agregar los recursos al evento
    Route::get('/events/agregar/recurso/{event}/{resource}', [EventController::class, 'addResource'])->name('event.addResource');

    // Quitar al recurso de la reserva del espacio
    Route::get('/events/quitar/recurso/{reservedResource}/{event}', [EventController::class, 'removeResource'])->name('event.removeResource');
    
});


Route::middleware(['role:Coordinador|Gestor de espacios'])->group(function () {
    // Ruta para encontrar disponibilidad de horario para reservar
    Route::get('/eventos/agenda', [EventController::class, 'byDay'])->name('events.byDay');

});

// Ruta para acceder a los eventos de un usuario
Route::get('/mis_eventos', [EventController::class, 'myEvents'])->name('events.my-events');

// Ruta para mostrar los detalles de un evento específico
Route::get('/evento/detalle/{event}', [EventController::class, 'show'])->name('events.show');

// Ruta para mostrar la lista de eventos
//Route::get('/eventos', [EventController::class, 'index'])->name('events.index');

// // Ruta para acceder a la creación de eventos
// Route::get('/evento/nuevo', [EventController::class,'create'])->name('events.create');



// // Ruta para guadar los participantes de un evento
// Route::get('/evento/{event}/menu_edicion', [EventController::class, 'menuEdit'])->name('events.menuEdit');

// // Ruta para mostrar el formulario de edición de un evento
// Route::get('/eventos/{id}/modificar', [EventController::class, 'edit'])->name('events.edit');

// // Ruta para actualizar los datos de un evento en la base de datos
// Route::put('/eventos/{id}', [EventController::class, 'update'])->name('events.update');

// // ruta para la revisión de eventos y aprobarlos o rechazarlos
// Route::get('/review-events', [EventController::class, 'reviewEvents'])->name('events.review-events');

// // ruta para actualizar el estatus de un evento
// Route::put('/events/validacion/{event}', [EventController::class, 'validar'])->name('events.validar');




