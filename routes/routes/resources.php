<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResourceController;

Route::middleware(['role:Administrador'])->group(function () {

    Route::post('/recursos/status/{resource}', [ResourceController::class,'toggleStatus'])->name('resource.toggleStatus');

    Route::delete('/recursos/delete/{resource}', [ResourceController::class,'delete'])->name('resource.delete');

});