<?php

use Illuminate\Support\Facades\Route;
// routes/routes/spaces.php

use App\Http\Controllers\SpaceController;

Route::middleware(['role:Administrador|Coordinador|Gestor de eventos'])->group(function () {
        // Buscador de espacios disponibles
        Route::get('/buscador_de_espacios', [SpaceController::class,'search'])->name('spaces.search');

        // Buscador de espacios disponibles
        Route::post('/buscador_de_espacios', [SpaceController::class,'search'])->name('spaces.search');

        // Consultar excepciones de horarios de cada espacio
        Route::get('/espacios/excepciones',[SpaceController::class,'exceptions'])->name('spaces.exceptions');
});

Route::middleware(['role:Coordinador|Gestor de espacios'])->group(function () {
    // Ruta para mostrar la lista de espacios
    Route::get('/mis_espacios', [SpaceController::class, 'my_spaces'])->name('spaces.my-spaces');

});

Route::middleware(['role:Administrador'])->group(function () {
    // Ruta para mostrar la lista de espacios
    Route::get('/espacio', [SpaceController::class, 'index'])->name('spaces.index');

    // Ruta para mostrar el formulario de creación de espacio
    Route::get('/espacio/nuevo', [SpaceController::class, 'create'])->name('spaces.create');

    // Ruta para guardar el nuevo espacio en la base de datos
    Route::post('/espacio', [SpaceController::class, 'store'])->name('spaces.store');

    // Ruta para mostrar los detalles de un espacio específico
    Route::get('/espacio/{id}', [SpaceController::class, 'show'])->name('spaces.show');

    // Ruta para mostrar el formulario de edición de un espacio
    Route::get('/espacio/{space}/edit', [SpaceController::class, 'edit'])->name('spaces.edit');

    // Ruta para actualizar los datos de un espacio en la base de datos
    Route::put('/espacio/{space}', [SpaceController::class, 'update'])->name('spaces.update');

    // Ruta para eliminar un espacio
    Route::delete('/espacio/{space}', [SpaceController::class, 'destroy'])->name('spaces.destroy');

});

Route::middleware(['role:Administrador|Coordinador|Gestor de espacios'])->group(function () {
    // Cambiar la disponibilidad de un espacio
    Route::put('/spaces/{space}/toggle-availability', [SpaceController::class, 'toggleAvailability'])
     ->name('spaces.toggleAvailability');
});