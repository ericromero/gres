<?php

use Illuminate\Support\Facades\Route;
// routes/routes/spaces.php

use App\Http\Controllers\SpaceExceptionController;

Route::middleware(['role:Administrador|Coordinador|Gestor de espacios'])->group(function () {
    // Agregar una nueva excepción de horario
    Route::post('/espacios/{space}/excepciones', [SpaceExceptionController::class, 'storeException'])
         ->name('spaces.exceptions.store');

    // Eliminar una excepción de horario existente
    Route::delete('/espacios/{space}/excepciones/{exception}', [SpaceExceptionController::class, 'destroyException'])
         ->name('spaces.exceptions.destroy');

});