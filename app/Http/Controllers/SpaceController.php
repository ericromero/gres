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
use App\Models\ReservationSetting;

// Establecer el locale en español
Carbon::setLocale('es');

class SpaceController extends Controller
{

    public function search(Request $request)
    {
        $reservationSettings = ReservationSetting::first();
        $allowedStartDate = $reservationSettings->start_date;
        $allowedEndDate = $reservationSettings->end_date;

        $rules = [
            'start_date' => [
                'date',
                'after_or_equal:' . now()->addWeekdays(4)->format('Y-m-d'), // Al menos 4 días hábiles desde hoy
                'after_or_equal:' . $allowedStartDate, // Igual o después de la fecha programada en el sistema
                'before_or_equal:' . $allowedEndDate, // Antes o igual a la fecha máxima del sistema
            ],
            'end_date' => [
                'date',
                'after_or_equal:start_date', // Después o igual a la fecha de inicio
                'before_or_equal:' . $allowedEndDate, // Antes o igual a la fecha máxima del sistema
            ],
            'start_time' => 'date_format:H:i|after_or_equal:09:00|before_or_equal:18:00',
            'end_time' => 'date_format:H:i|after:start_time|before_or_equal:19:00',
        ];

        $messages = [
            'start_date.date' => 'El campo fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser al menos 4 días hábiles después de hoy y no antes de la fecha permitida por el sistema.',
            'start_date.before_or_equal' => 'La fecha de inicio no debe exceder la fecha de fin permitida en el sistema.',
            'end_date.date' => 'El campo fecha de término debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de término debe ser igual o posterior a la fecha de inicio.',
            'end_date.before_or_equal' => 'La fecha de término debe ser como máximo la fecha permitida en el sistema.',
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
        //$allEvents = Event::whereDoesntHave('canceledEvent')->get();
        //La linea anterior no omitía los eventos rechazados, Actualización 5 sept 2024
        $allEvents = Event::whereDoesntHave('canceledEvent')
            ->whereDoesntHave('eventSpaces', function ($query) {
                $query->where('status', '=', 'rechazado');
            })
            ->get();


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

        // Obtener las fechas de configuración de reservas
        $allowedStartDateText = $this->getStringDate($reservationSettings->start_date);
        $allowedEndDateText = $this->getStringDate($reservationSettings->end_date);
        
        // Fin del código para desplegar el calendario
        if (
            $request->input('start_date') == null ||
            $request->input('end_date') == null ||
            $request->input('start_time') == null ||
            $request->input('end_time') == null
        ) {
            return view('events.availablesearch',compact('events','allowedStartDate', 'allowedEndDate','allowedStartDateText','allowedEndDateText'));
        }

        $startDateTime = $request->input('start_date') . ' ' . $request->input('start_time');
        $endDateTime = $request->input('end_date') . ' ' . $request->input('end_time');

        $start_date=$request->input('start_date');
        $end_date=$request->input('end_date');
        $start_time=$request->input('start_time');
        $end_time=$request->input('end_time');

        // Se obtiene los eventos que se traslapan 
        $overlappingEventIds = $this->getOverlappingEventIds($start_date, $end_date, $start_time, $end_time);
        
        // Se obtiene la lista de espacios que no se pueden prestar ya que estan ocupados y hay traslape, se omiten los espacios que han sido cancelados
        $excludedSpaceIds = DB::table('event_spaces')
                    ->join('events', 'events.id', '=', 'event_spaces.event_id')
                    ->whereIn('event_spaces.event_id', $overlappingEventIds)
                    ->where('event_spaces.status', '!=', 'rechazado')
                    ->pluck('event_spaces.space_id');
                    

        // Obtiene el día de la semana en español
        $daySearch = Carbon::parse($start_date)->isoFormat('dddd');

        // Se obtiene la lista de espacios que tienen excepciones para el día y hora buscados
        $excludedSpaceIdsForExceptions = Space::whereHas('exceptions', function ($query) use ($daySearch, $start_time, $end_time) {
            $query->where('day_of_week', $daySearch)
                ->whereTime('start_time', '<=', $end_time)
                ->whereTime('end_time', '>=', $start_time);
        })->pluck('id');

        // Combina los IDs de espacios excluidos por eventos y por excepciones
        $allExcludedSpaceIds = $excludedSpaceIds->merge($excludedSpaceIdsForExceptions)->unique();

        // Se obtiene la lista de los espacios disponibles, excluyendo aquellos que ya están ocupados o tienen excepciones
        $availableSpaces = Space::where('availability', true)
                        ->whereNotIn('id', $allExcludedSpaceIds)
                        ->get();

        $start_date_string=$this->getStringDate($start_date);
        $end_date_string=$this->getStringDate($end_date);

        return view('events.availablesearch', compact('availableSpaces','start_date','end_date','start_time','end_time','events','start_date_string','end_date_string','allowedStartDate', 'allowedEndDate','allowedStartDateText', 'allowedEndDateText'));
    }

    public function index() {
        // Obtener todos los espacios
        $spaces = Space::all();

        return view('spaces.index', compact('spaces'));
    }

    public function my_spaces() {        
        // Obtener el usuario actualmente autenticado (coordinador o gestor de espacios)
        $coordinator = Auth::user();

        // Obtenemos el departamento del usuario con base en su equipo de trabajo
        $coordinatedDepartment=Department::find($coordinator->team->department_id);
        

        // Obtener el departamento coordinado por el usuario
        //$coordinatedDepartment = $coordinator->coordinatedDepartment;

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

    // Muestra la vista con los documentos de lineamientos de cada espacio
    public function terms() {
        $spaces=Space::orderby('name','asc')->get();
        return view('terms',compact('spaces'));
    }

    // Actualizar el documento de lineamientos de un espacio
    public function updateTerms(Space $space, Request $request) {
        $request->validate([
            'terms' => 'required|file|mimes:pdf|max:5120',
        ]);

        // Procesar el documento PDF
        if ($request->hasFile('terms')) {
            $terms = $request->file('terms');
            $docName = time() . '_' . $terms->getClientOriginalName();

            // Borrar un documento anterior si existe
            if ($space->terms) {
                $oldImagePath = public_path($space->terms);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $terms->move(public_path('docs/spaces'), $docName);
            $space->terms = 'docs/spaces/' . $docName;
            $space->save();
            return back()->with('success', 'Documento actualizado correctamente.');
        }
        return back()->with('error', 'El documento no se pudo cargar.');
    }

    // Cambia la disponibilidad de un espacio
    public function toggleAvailability(Space $space)
    {
        $space->availability = !$space->availability;
        
        if($space->save()) {
            if(!$space->availability) {
                return back()->with('success', 'Espacio inhabilitado, no se pueden solicitar eventos en él.');
            } else {
                return back()->with('success', 'Espacio habilitado, se pueden recibir solicitudes de eventos en él.');
            }
        } else {
            return back()->with('error', 'No se pudo cambiar el estado del espacio.');
        }
    }

    // Listado de espacios con excepciones y estatus
    public function exceptions() {
        $spaces=Space::orderBy('name','asc')->get();
        return view('spaces.exceptions',compact('spaces'));
    }

    public function getOverlappingEventIds($start_date, $end_date, $start_time, $end_time) {
        $overlappingEventIds = Event::whereIn('status', ['solicitado', 'aceptado', 'finalizado'])
            ->whereDoesntHave('canceledEvent') // Excluir eventos cancelados
            ->whereDoesntHave('eventSpaces', function ($query) {
                $query->where('status', '=', 'rechazado'); // Excluir eventos con espacios rechazados
            })
            ->where(function ($query) use ($start_date, $end_date, $start_time, $end_time) {
                $query->where(function ($query) use ($start_date, $end_date) {
                    $query->where('start_date', '<=', $end_date)
                        ->where('end_date', '>=', $start_date);
                })
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

    private function getStringDate($date) {
        return Carbon::parse($date)->isoFormat('dddd D [de] MMMM [de] YYYY');
    }

}
