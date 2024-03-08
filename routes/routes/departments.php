<?php
use Illuminate\Support\Facades\Route;
// routes/routes/departments.php

use App\Http\Controllers\DepartmentController;

Route::middleware(['role:Administrador'])->group(function () {
    // Ruta para mostrar la lista de departamentos
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');

    // Ruta para mostrar el formulario de creación de departamento
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');

    // Ruta para guardar el nuevo departamento en la base de datos
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');

    // Ruta para mostrar los detalles de un departamento específico
    //Route::get('/departments/{id}', [DepartmentController::class, 'show'])->name('departments.show');

    // Ruta para mostrar el formulario de edición de un departamento
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');

    // Ruta para actualizar los datos de un departamento en la base de datos
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');

    // Ruta para eliminar un departamento
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Buscador de espacios disponibles
    Route::get('/recursos/{department}', [DepartmentController::class,'resources'])->name('department.resources');

    // Buscador de espacios disponibles
    Route::post('/recursos/agregar/{department}', [DepartmentController::class,'addResources'])->name('department.addResources');

});
