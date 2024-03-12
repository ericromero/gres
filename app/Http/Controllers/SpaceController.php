<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Space;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\Resource;

class SpaceController extends Controller
{

    public function search(Request $request)
    {
        $rules = [
            'start_date' => 'date|after_or_equal:' . now()->addWeekdays(4)->format('Y-m-d'),
            'end_date' => 'date|after_or_equal:start_date|before_or_equal:' . now()->addMonths(6)->format('Y-m-d'),
            'start_time' => 'date_format:H:i|after_or_equal:09:00|before_or_equal:18:00',
            'end_time' => 'date_format:H:i|after:start_time|before_or_equal:19:00',
        ];

        $messages = [
            'start_date.date' => 'El campo fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser al menos 4 días hábiles después de hoy.',
            'end_date.date' => 'El campo fecha de término debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de término debe ser después o igual a la fecha de inicio.',
            'end_date.before_or_equal' => 'La fecha de término debe ser como máximo dentro de los próximos 6 meses.',
            'start_time.date_format' => 'El campo hora de inicio debe tener el formato de hora HH:MM.',
            'start_time.after_or_equal' => 'La hora de inicio debe ser como mínimo a las 09:00.',
            'start_time.before_or_equal' => 'La hora de inicio debe ser como máximo a las 18:00.',
            'end_time.date_format' => 'El campo hora de término debe tener el formato de hora HH:MM.',
            'end_time.after' => 'La hora de término debe ser posterior a la hora de inicio.',
            'end_time.before_or_equal' => 'La hora de término debe ser como máximo a las 19:00.',
        ];
    
        $validator = Validator::make($request->all(), $rules, $messages);
    
        if ($validator->fails()) {
            return redirect()->route('dashboard')->with('error', $validator->errors()->first())->withInput();
        }

        // Obtiene solo los eventos que no tienen espacios rechazados
        $allEvents = Event::whereDoesntHave('eventSpaces', function ($query) {
            $query->where('status', '=', 'rechazado');
        })->get();

        $events = [];
        foreach ($allEvents as $event) {
            // Convertir las fechas de inicio y fin a objetos Carbon para poder manipularlas
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date . ' ' . $event->start_time);
            $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $event->end_date . ' ' . $event->end_time);

            // Añadir un evento por cada día entre la fecha de inicio y la fecha de finalización
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // Asegurarse de que el evento no se extienda más allá de la fecha de finalización y hora
                $endDateTime = $date->copy()->setTimeFrom(Carbon::createFromFormat('H:i:s', $event->end_time));
                if ($endDateTime->gt($endDate)) {
                    $endDateTime = $endDate->copy();
                }

                $events[] = [
                    'title' => $event->title,
                    'start' => $date->toDateTimeString(),
                    'end' => $endDateTime->toDateTimeString(),
                    'id' => $event->id,
                ];
            }
        }
        // Fin del código para desplegar el calendario

        if (
            $request->input('start_date') == null ||
            $request->input('end_date') == null ||
            $request->input('start_time') == null ||
            $request->input('end_time') == null
        ) {
            return view('events.availablesearch',compact('events'));
        }

        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'start_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):(?:[03]0|00)$/'],
            'end_time' => ['required', 'regex:/^(?:[01]\d|2[0-3]):(?:[03]0|00)$/', 'after:start_time'],
        ], [
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required' => 'La fecha de finalización es obligatoria.',
            'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.regex' => 'La hora de inicio debe tener minutos en 00 o 30.',
            'end_time.required' => 'La hora de finalización es obligatoria.',
            'end_time.regex' => 'La hora de finalización debe tener minutos en 00 o 30.',
            'end_time.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
        ]);

        $startDateTime = $request->input('start_date') . ' ' . $request->input('start_time');
        $endDateTime = $request->input('end_date') . ' ' . $request->input('end_time');

        $start_date=$request->input('start_date');
        $end_date=$request->input('end_date');
        $start_time=$request->input('start_time');
        $end_time=$request->input('end_time');

        // Se obtiene los eventos que se traslapan 
        $overlappingEventIds = $this->getOverlappingEventIds($start_date, $end_date, $start_time, $end_time);

        // Se obtiene la lista de espacios que no se pueden prestar ya que estan ocupados en eventos ya traslapados, excluyendo los que tienen un estado 'rechazado'
        $excludedSpaceIds = DB::table('event_spaces')
                    ->join('events', 'events.id', '=', 'event_spaces.event_id')
                    ->whereIn('event_spaces.event_id', $overlappingEventIds)
                    ->where('event_spaces.status', '!=', 'rechazado')
                    ->pluck('event_spaces.space_id');

        // Se obtiene la lista de los espacios disponibles, excluyendo aquellos que ya están ocupados
        $availableSpaces = DB::table('spaces')
                    ->whereNotIn('id', $excludedSpaceIds)
                    ->get();


        return view('events.availablesearch', compact('availableSpaces','start_date','end_date','start_time','end_time','events'));
    }

    public function index() {
        // Obtener todos los espacios
        $spaces = Space::all();

        return view('spaces.index', compact('spaces'));
    }

    public function my_spaces() {        
        // Obtener el usuario actualmente autenticado (coordinador)
        $coordinator = Auth::user();

        // Obtener el departamento coordinado por el usuario
        $coordinatedDepartment = $coordinator->coordinatedDepartment;

        // Variable para almacenar los recursos del
        $resources=null;

        // Verificar si el usuario es responsable de algún departamento
        if ($coordinatedDepartment) {
            // Obtener los espacios correspondientes al departamento coordinado
            $spaces = $coordinatedDepartment->spaces;

            // Obtener los recursos del departamento en cuestión            
            foreach($spaces as $space) {
                $resources=Resource::where('department_id',$space->department_id)
                ->orderBy('name','asc')
                ->get();
            }            
        } else {
            // Si el usuario no es responsable de ningún departamento, inicializar una colección vacía
            $spaces = collect();
        }

        return view('spaces.mis-espacios', compact('spaces','coordinatedDepartment','resources'));
    }

    public function create()
    {
        $departments = Department::all(); // Obtén la lista de departamentos

        return view('spaces.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'capacity' => 'required|integer|min:1|max:150',
            'photography' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Procesar y guardar la imagen
        if ($request->hasFile('photography')) {
            $photography = $request->file('photography');
            $imageName = time() . '_' . $photography->getClientOriginalName();
            $photography->move(public_path('images/spaces'), $imageName);
        }

        $space = new Space();
        $space->name = $request->input('name');
        $space->location = $request->input('location');
        $space->capacity = $request->input('capacity');
        $space->photography = 'images/spaces/' . $imageName;
        $space->department_id = $request->input('department_id');
        $space->save();

        return redirect()->route('spaces.index')->with('success', 'El espacio creado.');
    }

    public function edit(Space $space)
    {
        $departments = Department::all(); // Obtener la lista de departamentos

        return view('spaces.edit', compact('space', 'departments'));
    }

    public function update(Request $request, Space $space)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
            'capacity' => 'required|integer|min:1|max:150',
            'photography' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'department_id' => 'required|exists:departments,id',
        ]);

        if ($request->hasFile('photography')) {
            $photography = $request->file('photography');
            $imageName = time() . '_' . $photography->getClientOriginalName();
            $photography->move(public_path('images/spaces'), $imageName);

            // Borrar la imagen anterior si existe
            if ($space->photography) {
                $oldImagePath = public_path($space->photography);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $space->photography = 'images/spaces/' . $imageName;
        }

        $space->name = $request->input('name');
        $space->location = $request->input('location');
        $space->capacity = $request->input('capacity');
        $space->department_id = $request->input('department_id');
        $space->save();

        return redirect()->route('spaces.index')->with('success', 'El espacio ha sido actualizado exitosamente.');
    }

    public function destroy(Space $space)
    {
        // Borrar la imagen si existe
        if ($space->photography) {
            $imagePath = public_path($space->photography);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $space->delete();

        return redirect()->route('spaces.index')->with('success', 'El espacio ha sido eliminado exitosamente.');
    }

    public function getOverlappingEventIds($start_date, $end_date, $start_time, $end_time) {
        $overlappingEventIds = DB::table('events')
            ->whereIn('status', ['solicitado', 'aceptado', 'finalizado'])
            ->where(function ($query) use ($start_date, $end_date, $start_time, $end_time) {
                $query->where('start_date', '<=', $end_date)
                    ->where('end_date', '>=', $start_date)
                    ->where(function ($query) use ($start_time, $end_time) {
                        $query->where('start_time', '<=', $end_time)
                            ->where('end_time', '>=', $start_time);
                    });
            })
            ->pluck('id');
    
        return $overlappingEventIds;
    }
    
    public function getSpacesInOverlappingEvents($start_date, $end_date, $start_time, $end_time) {
        $overlappingEventIds = $this->getOverlappingEventIds($start_date, $end_date, $start_time, $end_time);
    
        $spacesInOverlappingEvents = DB::table('event_spaces')
            ->whereIn('event_id', $overlappingEventIds)
            ->pluck('space_id')
            ->unique();
    
        return $spacesInOverlappingEvents;
    }

    private function getResources($department_id) {
        $department=Department::find($department_id);
        return $department->resources;
    }

}
