<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CanceledEvent;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventParticipant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Space;
use App\Models\EventType;
use App\Models\ParticipationType;
use App\Models\User;
use App\Models\EventSpace;
use App\Models\EventStreaming;
use App\Models\EventRecording;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestSpaceEmail;
use App\Mail\RequestRecordEmail;
use App\Mail\RequestStreamingEmail;
use Illuminate\Support\Str;
use App\Mail\WelcomeMailParticipant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Adscription;
use App\Mail\NewEventMail;
use App\Models\Audience;
use App\Models\KnowledgeArea;
use App\Models\EventCategory;


class EventController extends Controller
{
    public function cartelera() {
        $now = Carbon::now();
    
        $events = Event::where('published', true)
            ->where('end_date', '>', $now)
            ->whereDoesntHave('eventSpaces', function ($query) {
                $query->where('status', '=', 'rechazado');
            })
            ->get();
    
        foreach ($events as $event) {
            $start_date = Carbon::createFromFormat('Y-m-d', $event->start_date);
            $end_date = Carbon::createFromFormat('Y-m-d', $event->end_date);
    
            $date_time_text = '';
    
            // Si el evento comienza y termina en el mismo día
            if ($start_date->isSameDay($end_date)) {
                $date_time_text = ucfirst($start_date->isoFormat('dddd D [de] MMMM')) . ' de ' . substr($event->start_time, 0, 5) . ' a ' . substr($event->end_time, 0, 5) . ' horas';
            } else {
                // Si el evento abarca varios días
                $start_text = $start_date->isoFormat('D [de] MMMM');
                $end_text = $end_date->isoFormat('D [de] MMMM');
                if ($start_date->month === $end_date->month) {
                    $start_text = substr($start_text, 0, strpos($start_text, 'de'));
                }
                $date_time_text = 'Del ' . $start_date->isoFormat('dddd') . ' ' . $start_text . ' al ' . $end_date->isoFormat('dddd') . ' ' . $end_text . ' de ' . substr($event->start_time, 0, 5) . ' a ' . substr($event->end_time, 0, 5) . ' horas';
            }
    
            $event->date_time_text = $date_time_text;
        }
    
        return view('welcome', compact('events'));
    }

    public function myEvents()
    {
        $events = Event::where('responsible_id', Auth::user()->id)
            ->orWhere('coresponsible_id', Auth::user()->id)
            ->orderBy('created_at', 'desc') 
            ->paginate(8);
        return view('events.my-events', compact('events'));
    }

    public function availableSearch() {
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
        return view('events.availablesearch',compact('events'));
    }

    public function create()
    {
        // Obtener los usuarios con departamento asignado
        $academicos = User::has('adscriptions.department')->get();
        
        // Obtener la lista de tipos de eventos disponibles
         $eventTypes = EventType::orderBy('name','asc')->get();

        return view('events.create', compact('eventTypes','academicos'));
    }

    public function createWithSpace(Request $request,$space,$start_date,$end_date,$start_time,$end_time)
    {
        // Obtén el usuario autenticado
        $user = Auth::user();

        // Lista de departamentos a los que pertenece el usuario
        $departments = $user->adscriptions->map(function ($adscription) {
            return $adscription->department;
        });

        $space=Space::find($request->space);
        // $start_date=$request->start_date;
        // $end_date=$request->end_date;
        // $start_time=$request->start_time;
        // $end_time=$request->end_time;

        // Obtener los usuarios con departamento asignado
        $academicos = User::has('adscriptions.department')->orderBy('name','asc')->get();

        // Obtener la lista de tipos de eventos disponibles
        $eventTypes = EventType::orderBy('name','asc')->get();

         // Obtener los tipos de audiencia
        $audiences = Audience::orderBy('name','asc')->get();

        // Obtener la lista de tipos de eventos disponibles
        $eventTypes = EventType::orderBy('name','asc')->get();

        // Obtener la lista de campos de conocimiento
        $knowledge_areas = KnowledgeArea::orderBy('name','asc')->get();

        // Obtener la lista de las categorias de tipos de eventos
        $categories = EventCategory::orderBy('name','asc')->get();
        

        return view('events.create', compact('space','eventTypes','start_date','end_date','start_time','end_time','academicos','departments','audiences','eventTypes','knowledge_areas','categories'));
    }

    public function store(Request $request)
    {
        $rules = [
            'title' => [
                'required',
                'string',
                'max:250'],
            'summary' => [
                'required',
                'string',
                'nullable',
                'max:500'],
            'start_date' => [
                'required',
                'date','
                after_or_equal:' . now()->addDays(4)->format('Y-m-d')],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date'],
            'start_time' => [
                'required',
                'date_format:H:i',
                'after_or_equal:07:00',
                'before:end_time'],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                'before_or_equal:21:00'],
                'audience' => 'required|integer|exists:audiences,id',
            'modality' => ['required', Rule::in(['Presencial', 'En línea', 'Mixta'])],
            'scope' => ['required', Rule::in(['Nacional', 'Internacional'])],
            'project_type' => ['required', Rule::in(['Abierto', 'Cerrado'])],
            'gender_equality' => ['required', Rule::in(['No', 'Equidad de género', 'Estadísticas desagregadas por sexo', 'Género', 'Igualdad de género'])],
            'knowledge_area' => 'required|integer|exists:knowledge_areas,id',
            'cover_image' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120'],
            'program' => [
                'required',
                'file',
                'mimes:pdf',
                'max:5120',
                'nullable'
            ],
            'registration_url' => [
                'nullable',
                'required_if:registration_required,1'],
            
            'responsible' => [
                'required',  
                'distinct:coresponsible'],
                
            'coresponsible' => [
                'required', 
                'distinct:responsible'],
            'other' => [
                'nullable',
                'required_if:event_type_id,Other',
                'string',
                'max:250',
            ],
            'category' => [
                'nullable',
                'required_if:event_type_id,Other',
                'integer',
                'exists:event_categories,id',
            ],
            'other_responsible_name' => [
                'nullable',
                'required_if:responsible,other_responsible',
                'string',
                'max:250',
            ],
            'degree_responsible' => [
                'nullable',
                'required_if:responsible,other_responsible',
            ],
            'email_responsible' => [
                'nullable',
                'required_if:responsible,other_responsible',
                'email',
                'unique:users,email',
            ],
            'other_coresponsible_name' => [
                'nullable',
                'required_if:coresponsible,other_coresponsible',
                'string',
                'max:250',
            ],
            'degree_coresponsible' => [
                'nullable',
                'required_if:coresponsible,other_coresponsible',
            ],
            'email_coresponsible' => [
                'nullable',
                'required_if:coresponsible,other_coresponsible',
                'email',
                'unique:users,email',
                Rule::unique('users', 'email')->where(function ($query) {
                    // Asegurarse de que el correo del corresponsable sea diferente al del responsable
                    $query->where('email', '!=', request('email_responsible'));
                }),
            ],
        ];
    
        $messages = [
            'title.required' => 'El título del evento es obligatorio.',
            'title.string' => 'El título del evento debe ser una cadena de texto.',
            'title.max' => 'El título del evento no puede exceder los 250 caracteres.',
            
            'summary.required' => 'El resumen del evento es obligatorio.',
            'summary.string' => 'El resumen del evento debe ser una cadena de texto.',
            'summary.max'=>'El resumen no debe exceder los 500 caracteres.',
            
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'start_date.after_or_equal' => 'La fecha de inicio debe ser igual o posterior a ' . now()->addDays(4)->format('Y-m-d'),
            
            'end_date.required' => 'La fecha de finalización es obligatoria.',
            'end_date.date' => 'La fecha de finalización debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de finalización debe ser igual o posterior a la fecha de inicio.',
            
            'start_time.required' => 'La hora de inicio es obligatoria.',
            'start_time.date_format' => 'La hora de inicio debe estar en formato HH:mm.',
            'start_time.after_or_equal' => 'La hora de inicio debe ser igual o posterior a las 07:00 AM.',
            'start_time.before' => 'La hora de inicio debe ser anterior a la hora de finalización.',
            
            'end_time.required' => 'La hora de finalización es obligatoria.',
            'end_time.date_format' => 'La hora de finalización debe estar en formato HH:mm.',
            'end_time.after' => 'La hora de finalización debe ser posterior a la hora de inicio.',
            'end_time.before_or_equal' => 'La hora de finalización debe ser igual o anterior a las 09:00 PM.',
            
            'cover_image.required' => 'Se requiere adjuntar una imagen de portada.',
            'cover_image.image' => 'El archivo de imagen de portada debe ser una imagen válida.',
            'cover_image.mimes' => 'Los formatos admitidos para la imagen de portada son .jpg, .jpeg y .png',
            'cover_image.max' => 'La imagen de portada es demasiado pesada, el tamaño máximo permitido es de 5 MB.',
            
            'program.required' => 'Se requiere adjuntar un programa en formato PDF.',
            'program.file' => 'El archivo del programa debe ser un archivo válido.',
            'program.mimes' => 'El formato admitido para el programa es PDF.',
            'program.max' => 'El archivo del programa es demasiado pesado, el tamaño máximo permitido es de 5 MB.',
            
            'registration_required.boolean' => 'El campo "Registro requerido" debe ser verdadero o falso.',
            'registration_url.required_if' => 'La URL de registro es obligatoria cuando el registro es requerido.',
                      
            'responsible.required' => 'El campo "Responsable" es obligatorio.',
            'responsible.distinct' => 'El responsable y el corresponsable deben ser usuarios diferentes.',
            
            'coresponsible.required' => 'El campo "Corresponsable" es obligatorio.',
            'coresponsible.distinct' => 'El corresponsable y el responsable deben ser usuarios diferentes.',

            'other.required_if' => 'El campo "Otro" es obligatorio cuando el tipo de evento es "Otro".',
            'other.string' => 'El campo "Otro" debe ser una cadena de texto.',
            'other.max' => 'El campo "Otro" no debe exceder los 250 caracteres.',

            'other_responsible_name.required_if' => 'Ingresa el nombre del responsable',
            'degree_responsible.required_if' => 'Selecciona el Grado académico',
            'email_responsible.required_if' => 'Ingresa el correo electrónico del corresponsable',
            'email_responsible.email' => 'El correo electrónico invalido.',
            'email_responsible.unique' => 'Ya hay un usuario registrado con este correo electrónico.',

            'other_coresponsible_name.required_if' => 'Ingresa el nombre del corresponsable.',
            'degree_coresponsible.required_if' => 'Seleciona el Grado académico',
            'email_coresponsible.required_if' => 'El campo "Correo electrónico del otro corresponsable" es obligatorio cuando seleccionas "Otro corresponsable".',
            'email_coresponsible.email' => 'El correo electrónico invalido.',
            'email_coresponsible.unique' => 'Ya hay un usuario registrado con este correo electrónico.',
        ];
    
        $validatedData = $request->validate($rules, $messages);

        $eventType = $request->input('event_type_id');

        if ($eventType == 'Other') {
            // Si el tipo de evento es "Other", crea un nuevo tipo de evento
            $newEventType = new EventType();
            $newEventType->name = $request->input('other');
            $newEventType->event_category_id=$request->input('category');
            $newEventType->register_by = Auth::id();
            $newEventType->save();

            // Actualiza el valor de event_type_id para ser el ID del nuevo tipo de evento
            $request->merge(['event_type_id' => $newEventType->id]);
        }

        // Validar y crear un nuevo responsable si es seleccionado "otro responsable"
        $responsibleId = $request->input('responsible');
        if ($responsibleId == 'other_responsible') {
            $external=null;
            if($request->external_responsible=='1') {
                $external=$request->external_responsible;
            }
            $name=$request->other_responsible_name;
            $degree=$request->degree_responsible;
            $email=$request->email_responsible;
            $external=$request->external_responsible;
            $newResponsible = $this->createNewUser($name,$degree,$email,$external);
            $responsibleId = $newResponsible->id;
        }

        // Validar y crear un nuevo corresponsable si es seleccionado "otro corresponsable"
        $coresponsibleId = $request->input('coresponsible');
        if ($coresponsibleId == 'other_coresponsible') {
            $external=null;
            if($request->external_coresponsible=='1') {
                $external=$request->external_coresponsible;
            }
            $name=$request->other_coresponsible_name;
            $degree=$request->degree_coresponsible;
            $email=$request->email_coresponsible;
            $newCoresponsible = $this->createNewUser($name,$degree,$email,$external);
            $coresponsibleId = $newCoresponsible->id;
        }

        // Guardar datos del evento
        $user = Auth::user();
        $event = new Event();
        $event->responsible_id = $responsibleId;
        $event->coresponsible_id = $coresponsibleId;
        $event->register_id = $user->id;
        $event->department_id = $request->input('department');
        $event->title = $request->input('title');
        $event->summary = $request->input('summary');
        $event->start_date = $request->input('start_date');
        $event->end_date = $request->input('end_date');
        $event->start_time = $request->input('start_time');
        $event->end_time = $request->input('end_time');
        $event->audience_id = $request->input('audience');
        $event->modality = $request->input('modality');
        $event->scope = $request->input('scope');
        $event->project_type = $request->input('project_type');
        $event->gender_equality = $request->input('gender_equality');
        $event->knowledge_area_id = $request->input('knowledge_area');
        // $event->space_id = $request->input('space_id');
        $event->registration_required  = $request->has('registration_required');
        $event->registration_url = $request->input('registration_url');

        // Guardar la imagen de portada
        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image');
            $imageName = time() . '_' . $coverImage->getClientOriginalName();
            $coverImage->move(public_path('images/events'), $imageName);
            $event->cover_image = 'images/events/' . $imageName;
        }
        
        // Guardar el tipo de evento
        $event->event_type_id = $request->input('event_type_id');

        // Guardar el programa si está presente
        if ($request->hasFile('program')) {
            $programFile = $request->file('program');
            $programName = time() . '_' . $programFile->getClientOriginalName();
            $programFile->move(public_path('program_files'), $programName);
            $event->program = 'program_files/' . $programName;
        }

        $event->transmission_required = $request->has('transmission_required');
        $event->recording_required = $request->has('recording_required');

        if($request->input('space')!=null) {
            $event->space_required=true;
        }

        $event->save();

        // Se debe almacenar la información para validar transmision, grabacion y espacio
        // Gestión de espacio
        if($request->input('space')!=null) {
            $eventSpace = new EventSpace();
            $eventSpace->event_id=$event->id;
            $eventSpace->space_id=$request->input('space');
            $eventSpace->save();
        }

        // Gestión de tranmisión
        if(isset($event->transmission_required)&&$event->transmission_required==true) {
            $eventBroadcast = new EventStreaming();
            $eventBroadcast->event_id=$event->id;
            $eventBroadcast->save();
        }

        // Gestión de espacio
        if(isset($event->recording_required)&&$event->recording_required==true) {
            $eventRecording = new EventRecording();
            $eventRecording->event_id=$event->id;
            $eventRecording->save();
        }

        return redirect()->route('events.participants',$event->id);
        //return redirect()->route('events.my-events')->with('success', 'El evento ha sido creado exitosamente.');
    }

    public function edit(Event $event)
    {
        // Solo se pueden editar eventos no publicados y vigentes
        // if($event->published==1||$event->start_date>=now()) {
        //     return redirect()->route('events.byArea')->with('error','El evento no puede ser actualizado.');
        // }

        // Obtén el usuario autenticado
        $user = Auth::user();

        // Lista de departamentos a los que pertenece el usuario
        $departments = $user->adscriptions->map(function ($adscription) {
            return $adscription->department;
        });

        // Obtener los usuarios con departamento asignado
        $academicos = User::has('adscriptions.department')->orderBy('name','asc')->get();

        // Obtener la lista de tipos de eventos disponibles
         $eventTypes = EventType::orderBy('name','asc')->get();

        return view('events.edit', compact('event','eventTypes','academicos','departments'));
    }

    public function update(Event $event, Request $request) {
        $rules = [
            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120'],
            'program' => [
                'file',
                'mimes:pdf',
                'max:5120',
                'nullable'
            ],
        ];
    
        $messages = [
            'cover_image.image' => 'El archivo de imagen de portada debe ser una imagen válida.',
            'cover_image.mimes' => 'Los formatos admitidos para la imagen de portada son .jpg, .jpeg y .png',
            'cover_image.max' => 'La imagen de portada es demasiado pesada, el tamaño máximo permitido es de 5 MB.',
            
            'program.file' => 'El archivo del programa debe ser un archivo válido.',
            'program.mimes' => 'El formato admitido para el programa es PDF.',
            'program.max' => 'El archivo del programa es demasiado pesado, el tamaño máximo permitido es de 5 MB.',
        ];

        $validatedData = $request->validate($rules, $messages);

        // en caso de recibir un nuevo banner, se elimina el anterior y se agrega el nuevo
        if(isset($request->cover_image)&&$request->cover_image!=null) {
            // Eliminar la imagen de portada si existe
            if ($event->cover_image) {
                $imagePath = public_path($event->cover_image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Guardar la imagen de portada
            if ($request->hasFile('cover_image')) {
                $coverImage = $request->file('cover_image');
                $imageName = time() . '_' . $coverImage->getClientOriginalName();
                $coverImage->move(public_path('images/events'), $imageName);
                $event->cover_image = 'images/events/' . $imageName;
            }
        }
        
        // en caso de recibir un nuevo programa, se elimina el anterior y se agrega el nuevo
        if(isset($request->program)&&$request->program!=null) {
            // Eliminar el cartel o programa de portada si existe
            if ($event->program) {
                $programPath = public_path($event->program);
                if (file_exists($programPath)) {
                    unlink($programPath);
                }
            }

            // Guardar el programa si está presente
            if ($request->hasFile('program')) {
                $programFile = $request->file('program');
                $programName = time() . '_' . $programFile->getClientOriginalName();
                $programFile->move(public_path('program_files'), $programName);
                $event->program = 'program_files/' . $programName;
            }
        }
        
        if($event->save()) {
            return redirect()->route('events.participants.update',$event->id)->with('success','Información del evento actualizada correctamente');
        } else {
            return redirect()->route('dashboard')->with('error','No se pudo actualizar el evento, inténtelo nuevamente.');
        }        
    }

    public function reviewEvents()
    {
        // Obtener todos los eventos pendientes de revisión
        $events = Event::where('status', 'solicitado')->get();
        return view('events.review-events', compact('events'));
    }

    public function validar(Request $request, Event $event)
    {
       
        $request->validate([
            'status' => 'required|in:aceptado,rechazado',
        ]);

        // Obtener el usuario autenticado
        $user = Auth::user();

        $event->status=$request->status;
        $event->validate_by=$user->id;

        if ($request->input('status') === 'rechazado') {
            $request->validate([
                'cancellation_reason' => 'required',
            ]);

            // Actualiza la razón de cancelación
            $canceled_event=new CanceledEvent();
            $canceled_event->event_id=$event->id;
            $canceled_event->canceled_by_user_id=$user->id;
            $canceled_event->cancellation_reason=$request->cancellation_reason;
            $canceled_event->save();
        }

        $event->save();

        return redirect()->route('events.review-events')
            ->with('success', 'Evento '.$request->input('status').'.');
    }

    public function publish($id)
    {
        $user=Auth::user();
        $event = Event::findOrFail($id);

        // Se verifica que el evento no tenga rechazado el prestamo de espacio para poder publicar
        
        $rechazado=false;
        if ($event->space_required) {
            foreach($event->spaces as $eventspace) {
                $eventSpaceStatus = $eventspace->pivot->status;
                if($eventSpaceStatus == "rechazado"&&$event->status!="borrador") {
                    $rechazado=true;
                }
            }
        }

        if($rechazado) {
            return redirect()->route('dashboard')->with('error', 'Acceso ilegal para publicar un evento');
        }

        $event->update(['published' => true,'published_by'=>$user->id]);

        // Notificación al responsable y coordiandor del evento
        $this->notifyPublish($event);

        return redirect()->route('dashboard')->with('success', 'El evento ha sido publicado exitosamente.');
    }

    public function registrarParticipantes(Event $event) {
        // Obtener los tipos de participantes
        $participationTypes = ParticipationType::all();
        $participants=EventParticipant::where('event_id',$event->id)->get();
        $academics = User::has('adscriptions.department')->get();
        return view('events.eventparticipants', compact('event', 'participationTypes','participants','academics'));
    }

    public function actualizarParticipantes(Event $event) {
        // Obtener los tipos de participantes
        $participationTypes = ParticipationType::all();
        $participants=EventParticipant::where('event_id',$event->id)->get();
        $academics = User::has('adscriptions.department')->get();
        return view('events.updateeventparticipants', compact('event', 'participationTypes','participants','academics'));
    }

    public function menuEdt(Event $event) {
        return view('event.menuedit',compact('event'));
    }

    public function destroy(Event $event)
    {
        // Eliminar los registros relacionados en event_participants si es que existen
        $event->users()->detach();
        
        // Eliminar la imagen de portada si existe
        if ($event->cover_image) {
            $imagePath = public_path($event->cover_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Eliminar el cartel o programa de portada si existe
        if ($event->program) {
            $programPath = public_path($event->program);
            if (file_exists($programPath)) {
                unlink($programPath);
            }
        }

        $event->delete();

        return redirect()->route('dashboard')->with('success', 'El registro del evento ha sido cancelado.');
    }

    public function register(Event $event) {
        // Se actualiza el estado del registro
        $event->status='solicitado';
        if($event->space_required=='0') {
            $event->status='finalizado';
        }
        $event->save();

        // Notificación al gestor de espacio
        if($event->space_required=='1') {
            $eventSpace=EventSpace::where('event_id',$event->id)->first();
            $space=Space::find($eventSpace->space_id);
            $user=User::find($space->department->responsible_id);
            Mail::to($user->email)->send(new RequestSpaceEmail($event, $space));            
        }

        // Notificación al gestor de grabación
        // if($event->recording_required!=null&&$event->recording_required==1) {
        //     Mail::to('udemat.psicologia@unam.mx')->send(new RequestRecordEmail($event, $space));
        // }

        // Notificación al gestor de transmisión 
        // if($event->transmission_required!=null&&$event->transmission_required==1) {
        //     Mail::to('udemat.psicologia@unam.mx')->send(new RequestStreamingEmail($event, $space));
        // }

        return redirect()->route('dashboard')->with('success', 'Evento registrado correctamente. Acceda a "Eventos de la coordinación" para dar seguimiento y publicarlo cuando así lo considere pertinente.');
    }

    public function by_area()
    {        
        $events=$this->eventsByDepartment();
        return view('events.by-area', compact('events'));
    }

    public function by_area_drafts()
    {        
        $user = Auth::user();

        // Obtén los IDs de los departamentos a los que el usuario está adscrito
        $departmentIds = $user->adscriptions->pluck('department_id');

        // Obtén los eventos que pertenecen a los departamentos del usuario
        $events = Event::whereIn('department_id', $departmentIds)
            ->where('status','borrador')
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('events.by-area', compact('events'));
    }

    public function by_area_unpublish()
    {   
        $usuarioDepartamentoId = Auth::user()->team->department_id;
        // Obtén los eventos que pertenecen a los departamentos del usuario
        $events=Event::join('event_spaces','events.id','=','event_spaces.event_id')
                ->where('events.status','finalizado')
                ->where('events.published','0')
                ->where('event_spaces.status','aceptado')
                ->where('department_id', $usuarioDepartamentoId)
                ->select('events.*')
                ->orderBy('events.created_at','desc')
                ->paginate(8);

        return view('events.by-area', compact('events'));
    }

    public function eventsByDepartment() {
        // Obtén el usuario autenticado
        $user = Auth::user();

        // Obtén los IDs de los departamentos a los que el usuario está adscrito
        $departmentIds = $user->adscriptions->pluck('department_id');

        // Obtén los eventos que pertenecen a los departamentos del usuario
        $events = Event::whereIn('department_id', $departmentIds)->orderBy('created_at', 'desc')->paginate(8);
        return $events;

    }

    public function calendario() {
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
        return view ('calendar',compact('events'));
    }

    public function show(Event $event) {
        //return $event->users;
        $participants=EventParticipant::where('event_id',$event->id)->get();
        return view('events.show',compact('event','participants'));
    }

    public function creditos() {
        return view('creditos');
    }

    private function createNewUser($name,$degree,$email,$external)
    {
        // Crear el nuevo usuario
        $newUser = new User();
        $newUser->name = $name;
        $newUser->degree = $degree;
        $newUser->email = $email;
        // Generar una contraseña aleatoria y establecerla
        $password = Str::random(10);
        $newUser->password = Hash::make($password);

        // Guardar el nuevo usuario en la base de datos
        $newUser->save();

        // Obtener el departamento del usuario logueado
        $loggedInUserDepartmentId = Auth::user()->team->department_id;

        // Registrar la adscripción del nuevo usuario
        if($external==null) {
            $external='0';
        }
        Adscription::create([
            'department_id' => $loggedInUserDepartmentId,
            'user_id' => $newUser->id,
            'external'=>$external,
        ]);

        // Enviar el correo al nuevo usuario
        Mail::to($newUser->email)->send(new WelcomeMailParticipant($newUser->email, $password));

        return $newUser;
    }

    private function notifyPublish(Event $event) {
        $emailList = [];
    
        // Notificaciones para anunciar que se ha publicado un nuevo evento
        $responsible = User::find($event->responsible_id);
    
        // Obtener el ID del departamento del usuario logueado
        $user = Auth::user();
        $userDepartmentId = $user->team->department_id;
    
        // Obtener la lista de correos electrónicos de usuarios en el mismo departamento
        $areaEmails = User::join('teams', 'users.id', '=', 'teams.user_id')
            ->where('teams.department_id', $userDepartmentId)
            ->pluck('email')
            ->toArray(); // Convertir la colección en una matriz
    
        $diffusionEmails = ['augarued@unam.mx', 'alejandramireles@psicologia.unam.mx', 'publicaciones.psicologia@unam.mx'];
    
        // Agregar los correos electrónicos de $areaEmails y $diffusionEmails a $emailList
        $emailList = array_merge($emailList, $areaEmails, $diffusionEmails);
    
        $mail = new NewEventMail($event,$emailList);
        Mail::to($responsible)->send($mail);
    }

    public function preCancel(Event $event) {
        return view('events.precancel',compact('event'));
    }

    public function cancel(Event $event, Request $request) {
        $request->validate([
            'justify' => 'required|string|min:100|max:2000', // Puedes agregar otras reglas de validación según necesites
        ], [
            'justify.required' => 'El motivo de la cancelación es obligatorio.',
            'justify.string' => 'El motivo de la cancelación debe ser texto.',
            'justify.min'=>'El motivo de la cancelación debe tener como mínimo 100 caracteres.',
            'justify.max' => 'El motivo de la cancelación no debe superar los 2000 caracteres.',
        ]);

        $event = Event::find($event->id); // Asegúrate de reemplazar esto con la forma correcta de obtener tu evento.
        $user=Auth::user();
        if ($event->cancelled || $event->status !== 'finalizado') {
            return redirect()->route('dashboard')->with('error', 'Acceso ilegal, no se puede cancelar el evento');
        }

        CanceledEvent::create([
            'event_id' => $event->id,
            'cancellation_reason' => $request->justify,
            'canceled_by_user_id' => $user->id,
        ]);

        $event->update([
            'cancelled' => 1,
        ]);
        
        return redirect()->route('dashboard')->with('success','Se ha cancelado el evento correctamente');
    }

    public function byDay() {
        $user=Auth::user();
        $departmentID=$user->team->department_id;
        $allEvents = Event::select('events.*')
            ->join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('spaces.department_id', $departmentID)
            ->where('events.published','1')
            ->where('events.status','finalizado')
            ->where('events.cancelled','0')
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
        return view('events.by_day',compact('events'));
    }
}
