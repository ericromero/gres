<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReservationSetting;

class ReservationSettingsController extends Controller
{
    // Mostrar el formulario de configuración
    public function edit()
    {
        // Obtener la configuración actual o crear una nueva si no existe
        $settings = ReservationSetting::firstOrCreate([]);

        return view('reservation_settings.edit', compact('settings'));
    }

    // Actualizar la configuración
    public function update(Request $request)
    {
        // Validación de los campos
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
        ]);

        try {
            // Obtener o crear la configuración
            $settings = ReservationSetting::firstOrCreate([]);
            $settings->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
    
            // Redirigir de vuelta con un mensaje de éxito
            return redirect()->back()->with('success', 'Configuración de reservas actualizada correctamente.');
    
        } catch (\Exception $e) {
            // En caso de error, redirigir de vuelta con un mensaje de error
            return redirect()->back()->with('error', 'Ocurrió un error al actualizar la configuración de reservas. Inténtalo de nuevo.');
        }
    }

}
