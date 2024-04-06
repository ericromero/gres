<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Space;

class SpaceExceptionController extends Controller
{
    // almacena la excepción para reserva de espacios
    public function storeException(Request $request, Space $space)
    {
        $validated = $request->validate([
            'day_of_week' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Verifica si hay traslapes con excepciones existentes
        $overlap = $space->exceptions()->where('day_of_week', $validated['day_of_week'])
        ->where(function ($query) use ($validated) {
            $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
        })->exists();

        if ($overlap) {
            return back()->with('error','La excepción de horario se traslapa con otra existente.');
        }

        $space->exceptions()->create($validated);

        return back()->with('success', 'Excepción de horario agregada correctamente.');
    }

    // Elmina la excepción de un espacio
    public function destroyException(Space $space, $exceptionId)
    {
        $space->exceptions()->findOrFail($exceptionId)->delete();

        return back()->with('success', 'Excepción de horario eliminada correctamente.');
    }
}
