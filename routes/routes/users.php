<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware(['role:Administrador'])->group(function () {
    // Ruta para mostrar la lista de usuarios
    Route::get('/usuarios', [UserController::class, 'index'])->name('users.index');

    // Ruta para guardar el nuevo usuario en la base de datos
    Route::post('/usuario/busqueda', [UserController::class, 'search'])->name('users.search');

    // Ruta para mostrar el formulario de edición de un usuario
    Route::get('/usuario/{user}/actualizar', [UserController::class, 'edit'])->name('users.edit');

    // Ruta para actualizar los datos de un usuario en la base de datos
    Route::put('/usuario/{user}', [UserController::class, 'update'])->name('users.update');

    // Ruta para mostrar el formulario de creación de usuario
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');

    // Ruta para guardar el nuevo usuario en la base de datos
    Route::post('/usuarios', [UserController::class, 'store'])->name('users.store');

    // Lista completa de usuarios
    Route::get('/usuarios/lista', [UserController::class, 'list'])->name('users.list');
});

Route::middleware(['role:Coordinador'])->group(function () {
    // Ruta para mostrar la lista de usuarios
    Route::get('/equipo', [UserController::class, 'team'])->name('users.team');

    // Ruta para mostrar el formulario de creación de usuario
    Route::get('/equipo/creaUsuario', [UserController::class, 'createUserTeam'])->name('users.createUserTeam');

    // Ruta para guardar el nuevo usuario en la base de datos
    Route::post('/equipo/agregaUsuario', [UserController::class, 'storeUserTeam'])->name('users.storeUserTeam');

    // Ruta para guardar el nuevo usuario en la base de datos
    //Route::post('/equipo/agregaNuevoUsuario', [UserController::class, 'storeNewUserTeam'])->name('users.storeNewUserTeam');

    // Ruta para quitar a un usuario del equipo
    Route::delete('/equipo/quitar/{team}', [UserController::class, 'removeTeam'])->name('users.removeTeam');

});

// Route::middleware(['role:Coordinador|Gestor de eventos'])->group(function () {
//     // Ruta para mostrar el formulario de creación de usuario
//     Route::post('/usuario/alta', [UserController::class, 'altaAcademicoPre'])->name('users.altaAcademicoPre');

//     // Ruta para mostrar el formulario de creación de usuario
//     Route::get('/usuario/alta', [UserController::class, 'altaAcademicoPre'])->name('users.altaAcademicoPre');

//     // Ruta para guardar el nuevo usuario en la base de datos
//     Route::post('/evento/usuario_agregado', [UserController::class, 'storePreEvent'])->name('users.storePreEvent');
// });

    

    
    



    

    // Ruta para mostrar los detalles de un usuario específico
    // Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');



    // Ruta para mostrar el formulario de edición de un usuario
   // Route::get('/usuario/{user}/actualizar', [UserController::class, 'edit'])->name('users.edit');

    



    // Ruta para eliminar un usuario
    //Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
