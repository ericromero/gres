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
use App\Models\EventResource;
use App\Models\Resource;
use App\Models\Team;
use App\Mail\CancelEventMail;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{

    public $diffusionEmails;
    
    public function __construct() {
        $this->diffusionEmails = ['augarued@unam.mx', 'alejandramireles@psicologia.unam.mx', 'publicaciones.psicologia@unam.mx'];
    }

    public function cartelera() {
        $now = Carbon::now();
    
        $events = Event::where('published', true)
            ->where('end_date', '>', $now)
            ->whereDoesntHave('eventSpaces', function ($query) {
                $query->where('status', '=', 'rechazado');
            })
            ->orderBy('start_date','asc')
            ->get();
        
        //return $events;
    
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

    public function calendario() {
        // Obtiene solo los eventos que no tienen espacios rechazados
        $allEvents = Event::whereDoesntHave('eventSpaces', function ($query) {
            $query->where('status', '=', 'rechazado');
            })
            ->where('published',true)
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
        return view ('calendar',compact('events'));
    }

    public function myEvents()
    {
        // ID del usuario actual
        $userId = Auth::id();

        // Obtiene todos los eventos donde el usuario es responsable, coresponsable o participante
        $events = Event::where('responsible_id', $userId)
        ->orWhere('coresponsible_id', $userId)
        ->orWhereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->orderBy('start_date', 'asc')
        ->paginate(8);
        return view('events.my-events', compact('events'));
    }

    // public function availableSearchhh() {
    //     // Obtiene solo los eventos que no tienen espacios rechazados
    //     $allEvents = Event::whereDoesntHave('eventSpaces', function ($query) {
    //         $query->where('status', '=', 'rechazado');
    //     })->get();

    //     $events = [];
    //     foreach ($allEvents as $event) {
    //         // Convertir las fechas de inicio y fin a objetos Carbon para poder manipularlas
    //         $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $event->start_date . ' ' . $event->start_time);
    //         $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $event->end_date . ' ' . $event->end_time);

    //         // Añadir un evento por cada día entre la fecha de inicio y la fecha de finalización
    //         for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
    //             // Asegurarse de que el evento no se extienda más allá de la fecha de finalización y hora
    //             $endDateTime = $date->copy()->setTimeFrom(Carbon::createFromFormat('H:i:s', $event->end_time));
    //             if ($endDateTime->gt($endDate)) {
    //                 $endDateTime = $endDate->copy();
    //             }

    //             $events[] = [
    //                 'title' => $event->title,
    //                 'start' => $date->toDateTimeString(),
    //                 'end' => $endDateTime->toDateTimeString(),
    //                 'id' => $event->id,
    //             ];
    //         }
    //     }
    //     return view('events.availablesearch',compact('events'));
    //}

    public function create()
    {
        // Obtener los usuarios con departamento asignado
        $academicos = User::has('adscriptions.department')->get();
        
        // Obtener la lista de tipos de eventos disponibles
        $eventTypes = EventType::orderBy('name','asc')->get();
        return view('events.create', compact('eventTypes','academicos'));

    }

    public function createWithSpace(Request $request)
    {
        // Extrae los valores del formulario
        $spaceId = $request->input('space');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $start_time = $request->input('start_time');
        $end_time = $request->input('end_time');
        $start_date_string=$this->getStringDate($start_date);
        $end_date_string=$this->getStringDate($end_date);

        // Obtén el usuario autenticado
        $user = Auth::user();

        // Lista de departamentos a los que pertenece el usuario
        $departments = $user->adscriptions->map(function ($adscription) {
            return $adscription->department;
        });

        // Se verifica que el espacio seleccionado esté habilitado        
        $space=Space::find($spaceId);
        if($space!=null&&!$space->availability) {
            return back()->with('error', 'El espacio solicitado no está disponible.');
        }

        ///////// Este bloque verifica si el espacio seleccionado tiene una excepción de horario //////////////////////
        // Convertir las fechas a día de la semana
        $daySearch = Carbon::parse($start_date)->locale('es')->isoFormat('dddd');
        
        // Verificar si el espacio tiene excepciones para el día y hora buscados
        $hasException = $space->exceptions()->where('day_of_week', $daySearch)
                        ->whereTime('start_time', '<=', $end_time)
                        ->whereTime('end_time', '>=', $start_time)
                        ->exists();

        if ($hasException) {
            return back()->with('error', 'El espacio solicitado tiene una excepción de horario para la fecha y hora seleccionadas.');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
        
        if ($request->has('private')) {
            // El checkbox está marcado
            return view('events.create-private', compact('space','eventTypes','start_date','end_date','start_time','end_time','academicos','departments','audiences','eventTypes','knowledge_areas','categories','start_date_string','end_date_string'));
        }
        return view('events.create', compact('space','eventTypes','start_date','end_date','start_time','end_time','academicos','departments','audiences','eventTypes','knowledge_areas','categories','start_date_string','end_date_string'));
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
            'requirements' => [
                'string',
                'nullable',
                'max:500'],
            'contact_email' => ['nullable', 'email'],
            'website' => ['nullable', 'url'],
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
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120'],            
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
            'agreeTerms' => [
                'accepted',
            ],
        ];
    
        $messages = [
            'title.required' => 'El título del evento es obligatorio.',
            'title.string' => 'El título del evento debe ser una cadena de texto.',
            'title.max' => 'El título del evento no puede exceder los 250 caracteres.',
            
            'summary.required' => 'El resumen del evento es obligatorio.',
            'summary.string' => 'El resumen del evento debe ser una cadena de texto.',
            'summary.max'=>'El resumen no debe exceder los 500 caracteres.',

            'requirements.string' => 'Los requisitos adicionales deben ser una cadena de texto.',
            'requirements.max'=>'Los requisitos adicionales no deben exceder los 500 caracteres.',
            
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
            
            'cover_image.image' => 'El archivo de imagen de portada debe ser una imagen válida.',
            'cover_image.mimes' => 'Los formatos admitidos para la imagen de portada son .jpg, .jpeg y .png',
            'cover_image.max' => 'La imagen de portada es demasiado pesada, el tamaño máximo permitido es de 5 MB.',
            
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

            'contact_email.email' => 'Por favor, ingresa una dirección de correo electrónico válida.',

            'website.url' => 'Por favor, ingresa una URL válida para el sitio web.',

            'agreeTerms.accepted' => 'Es necesario indicar que ha leído y está deacuerdo con los lineamientos del espacio solicitado.',
        ];
    
        $validatedData = $request->validate($rules, $messages);

        // Se verifica que el spacio seleccionado esté habilitado
        if($request->input('space')!=null) {
            $space=Space::find($request->input('space'));
            if(!$space->availability) {
                return redirect()->route('spaces.search')->with('error', 'El espacio solicitado no está disponible.');
            }
        }

        ///////// Este bloque verifica si el espacio seleccionado tiene una excepción de horario //////////////////////
        // Convertir las fechas a día de la semana
        $daySearch = Carbon::parse($request->input('start_date'))->locale('es')->isoFormat('dddd');
        
        // Verificar si el espacio tiene excepciones para el día y hora buscados
        $hasException = $space->exceptions()->where('day_of_week', $daySearch)
                        ->whereTime('start_time', '<=', $request->input('end_time'))
                        ->whereTime('end_time', '>=', $request->input('start_time'))
                        ->exists();

        if ($hasException) {
            return redirect()->route('spaces.search')->with('error', 'El espacio solicitado tiene una excepción de horario para la fecha y hora seleccionadas.');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
        $event->contact_email = $request->filled('contact_email') ? $request->input('contact_email') : null;
        $event->website = $request->filled('website') ? $request->input('website') : null;
        $event->requirements = $request->filled('requirements') ? $request->input('requirements') : null;

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
        // if ($request->hasFile('program')) {
        //     $programFile = $request->file('program');
        //     $programName = time() . '_' . $programFile->getClientOriginalName();
        //     $programFile->move(public_path('program_files'), $programName);
        //     $event->program = 'program_files/' . $programName;
        // }

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

        // En caso de que se haya elegido el uso de un espacio físico, se envía a la pantalla para uso de recursos de dicho espacio
        if($request->input('space')!=null) {
            // si área del espacio solicitado tiene recursos se envía a la selección de los mismos caso contrario a selección de participantes
            if($this->isAvailableResources($event)) {
                return redirect()->route('event.selectResources',$event->id)->with('success','Información del evento actualizada correctamente');
            } else {
                return redirect()->route('events.participants',$event->id)->with('success','Información del evento actualizada correctamente');
            }
        }

        return redirect()->route('events.participants',$event->id);
        //return redirect()->route('events.my-events')->with('success', 'El evento ha sido creado exitosamente.');
    }

    public function storePrivate(Request $request)
    {
        $rules = [
            'title' => [
                'required',
                'string',
                'max:250'],
            
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
            
            'knowledge_area' => 'required|integer|exists:knowledge_areas,id',
            
            
            'responsible' => [
                'required',  
                'distinct:coresponsible'],
                
            
            'agreeTerms' => [
                'accepted',
            ],
        ];
    
        $messages = [
            'title.required' => 'El título del evento es obligatorio.',
            'title.string' => 'El título del evento debe ser una cadena de texto.',
            'title.max' => 'El título del evento no puede exceder los 250 caracteres.',
            
            'summary.required' => 'El resumen del evento es obligatorio.',
            'summary.string' => 'El resumen del evento debe ser una cadena de texto.',
            'summary.max'=>'El resumen no debe exceder los 500 caracteres.',

            'requirements.string' => 'Los requisitos adicionales deben ser una cadena de texto.',
            'requirements.max'=>'Los requisitos adicionales no deben exceder los 500 caracteres.',
            
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
            
            'cover_image.image' => 'El archivo de imagen de portada debe ser una imagen válida.',
            'cover_image.mimes' => 'Los formatos admitidos para la imagen de portada son .jpg, .jpeg y .png',
            'cover_image.max' => 'La imagen de portada es demasiado pesada, el tamaño máximo permitido es de 5 MB.',
            
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

            'contact_email.email' => 'Por favor, ingresa una dirección de correo electrónico válida.',

            'website.url' => 'Por favor, ingresa una URL válida para el sitio web.',

            'agreeTerms.accepted' => 'Es necesario indicar que ha leído y está deacuerdo con los lineamientos del espacio solicitado.',
        ];
    
        $validatedData = $request->validate($rules, $messages);

        // Se verifica que el spacio seleccionado esté habilitado
        if($request->input('space')!=null) {
            $space=Space::find($request->input('space'));
            if(!$space->availability) {
                return redirect()->route('spaces.search')->with('error', 'El espacio solicitado no está disponible.');
            }
        }

        ///////// Este bloque verifica si el espacio seleccionado tiene una excepción de horario //////////////////////
        // Convertir las fechas a día de la semana
        $daySearch = Carbon::parse($request->input('start_date'))->locale('es')->isoFormat('dddd');
        
        // Verificar si el espacio tiene excepciones para el día y hora buscados
        $hasException = $space->exceptions()->where('day_of_week', $daySearch)
                        ->whereTime('start_time', '<=', $request->input('end_time'))
                        ->whereTime('end_time', '>=', $request->input('start_time'))
                        ->exists();

        if ($hasException) {
            return redirect()->route('spaces.search')->with('error', 'El espacio solicitado tiene una excepción de horario para la fecha y hora seleccionadas.');
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////

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
        //$event->coresponsible_id = $coresponsibleId;
        $event->register_id = $user->id;
        $event->department_id = $request->input('department');
        $event->title = $request->input('title');
        //$event->summary = $request->input('summary');
        $event->start_date = $request->input('start_date');
        $event->end_date = $request->input('end_date');
        $event->start_time = $request->input('start_time');
        $event->end_time = $request->input('end_time');
        $event->private=true;
        $event->audience_id = $request->input('audience');
        $event->status='solicitado';
        $event->published=0;
        $event->cancelled=0;
        //$event->modality = $request->input('modality');
        //$event->scope = $request->input('scope');
        //$event->project_type = $request->input('project_type');
        //$event->gender_equality = $request->input('gender_equality');
        $event->knowledge_area_id = $request->input('knowledge_area');
        // $event->space_id = $request->input('space_id');
        //$event->registration_required  = $request->has('registration_required');
        //$event->registration_url = $request->input('registration_url');
        $event->contact_email = $request->filled('contact_email') ? $request->input('contact_email') : null;
        //$event->website = $request->filled('website') ? $request->input('website') : null;
        //$event->requirements = $request->filled('requirements') ? $request->input('requirements') : null;

        // Guardar la imagen de portada
        // if ($request->hasFile('cover_image')) {
        //     $coverImage = $request->file('cover_image');
        //     $imageName = time() . '_' . $coverImage->getClientOriginalName();
        //     $coverImage->move(public_path('images/events'), $imageName);
        //     $event->cover_image = 'images/events/' . $imageName;
        // }
        
        // Guardar el tipo de evento
        $event->event_type_id = $request->input('event_type_id');

        // Guardar el programa si está presente
        // if ($request->hasFile('program')) {
        //     $programFile = $request->file('program');
        //     $programName = time() . '_' . $programFile->getClientOriginalName();
        //     $programFile->move(public_path('program_files'), $programName);
        //     $event->program = 'program_files/' . $programName;
        // }

        //$event->transmission_required = $request->has('transmission_required');
        //$event->recording_required = $request->has('recording_required');

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

        // Notificación al responsable y departamento responsable del evento
        $this->notifyRegister($event);

        return redirect()->route('dashboard')->with('success', 'El evento ha sido creado exitosamente.');
    }

    public function edit(Event $event)
    {
        // No se puede editar eventos rápidos
        if($event->private==1) {
            return redirect()->route('events.byArea')->with('error','Los eventos privados no se pueden modificar.');
        }

        //Solo se pueden editar eventos no publicados y vigentes
        if($event->published==1) {
            return redirect()->route('events.byArea')->with('error','El evento ya está publicado, no puede modificarse.');
        }

        //Solo se pueden editar eventos no publicados y vigentes
        if($event->start_date<=now()) {
            return redirect()->route('events.byArea')->with('error','El evento ya está expiró, no puede modificarse.');
        }

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
        // No se puede editar eventos rápidos
        if($event->private==1) {
            return redirect()->route('events.byArea')->with('error','Los eventos privados no se pueden modificar.');
        }

        //Solo se pueden editar eventos no publicados y vigentes
        if($event->published==1) {
            return redirect()->route('events.byArea')->with('error','El evento ya está publicado, no puede modificarse.');
        }

        //Solo se pueden editar eventos no publicados y vigentes
        if($this->getEventExpired($event)) {
            return redirect()->route('events.byArea')->with('error','El evento ya está expiró, no puede modificarse.');
        }
        
        $rules = [
            'title' => [
                'nullable',
                'string',
                'max:250'],
            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:5120'],
            'contact_email' => ['nullable', 'email'],
            'website' => ['nullable', 'url'],
            'program' => [
                'file',
                'mimes:pdf',
                'max:5120',
                'nullable'
            ],
        ];
    
        $messages = [
            'title.string' => 'El título del evento debe ser una cadena de texto.',
            'title.max' => 'El título del evento no puede exceder los 250 caracteres.',

            'cover_image.image' => 'El archivo de imagen de portada debe ser una imagen válida.',
            'cover_image.mimes' => 'Los formatos admitidos para la imagen de portada son .jpg, .jpeg y .png',
            'cover_image.max' => 'La imagen de portada es demasiado pesada, el tamaño máximo permitido es de 5 MB.',
            
            'program.file' => 'El archivo del programa debe ser un archivo válido.',
            'program.mimes' => 'El formato admitido para el programa es PDF.',
            'program.max' => 'El archivo del programa es demasiado pesado, el tamaño máximo permitido es de 5 MB.',

            'contact_email.email' => 'Por favor, ingresa una dirección de correo electrónico válida.',

            'website.url' => 'Por favor, ingresa una URL válida para el sitio web.',
        ];

        $validatedData = $request->validate($rules, $messages);

        // en caso de recibir un nuevo banner, se elimina el anterior y se agrega el nuevo
        if(isset($request->title)&&$request->title!=null) {
            // Guardar el nuevo título
            $event->title = $request->title;            
        }

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
        // if(isset($request->program)&&$request->program!=null) {
        //     // Eliminar el cartel o programa de portada si existe
        //     if ($event->program) {
        //         $programPath = public_path($event->program);
        //         if (file_exists($programPath)) {
        //             unlink($programPath);
        //         }
        //     }

        //     // Guardar el programa si está presente
        //     if ($request->hasFile('program')) {
        //         $programFile = $request->file('program');
        //         $programName = time() . '_' . $programFile->getClientOriginalName();
        //         $programFile->move(public_path('program_files'), $programName);
        //         $event->program = 'program_files/' . $programName;
        //     }
        // }

        // En caso de existir, se guarda o actualiza el correo de contacto
        $event->contact_email = $request->filled('contact_email') ? $request->input('contact_email') : null;

        // En caso de existir, se guarda o actualiza el sitio web
        $event->website = $request->filled('website') ? $request->input('website') : null;
        
        if($event->save()) {
            // En caso de que se haya elegido el uso de un espacio físico, se envía a la pantalla para uso de recursos de dicho espacio
            if($event->space_required==1) {
                // si área del espacio solicitado tiene recursos se envía a la selección de los mismos caso contrario a selección de participantes
                if($this->isAvailableResources($event)) {
                    return redirect()->route('event.selectResources',$event->id)->with('success','Información del evento actualizada correctamente');
                } else {
                    return redirect()->route('events.participants.update',$event->id)->with('success','Información del evento actualizada correctamente');
                }
            } else {
                return redirect()->route('events.participants.update',$event->id)->with('success','Información del evento actualizada correctamente');
            }
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

        // No se puede publicar un evento que no tenga cartel
        if($event->cover_image==null) {
            return redirect()->route('events.byArea')->with('error', 'No puede publicar el evento hasta que haya subido el cartel del mismo.');
        }

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
        //Solo se pueden editar eventos no publicados y vigentes
        if($event->published==1) {
            return redirect()->route('events.byArea')->with('error','El evento ya está publicado, no puede modificarse.');
        }

        // Obtener los tipos de participantes
        $participationTypes = ParticipationType::all();
        $participants=EventParticipant::where('event_id',$event->id)->get();
        $academics = User::has('adscriptions.department')->get();
        return view('events.eventparticipants', compact('event', 'participationTypes','participants','academics'));
    }

    public function actualizarParticipantes(Event $event) {
        //Solo se pueden editar eventos no publicados y vigentes
        if($event->published==1) {
            return redirect()->route('events.byArea')->with('error','El evento ya está publicado, no puede modificarse.');
        }
        
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
        
        // Notificación al responsable y departamento responsable del evento
        $this->notifyRegister($event);

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

    public function by_area_filter(Request $request) {
        // Definir reglas de validación
        $rules = [
            'orderBy' => 'required|string|in:title,start_date',
            'orderByType' => 'required|string|in:asc,desc',
            'searchByField' => 'required|string|in:title,summary',
            'searchBy' => 'nullable|string|max:255',
        ];

        // Definir mensajes de error personalizados
        $messages = [
            'orderBy.required' => 'El campo Ordenar por es obligatorio.',
            'orderBy.string' => 'El campo Ordenar por debe ser una cadena de texto.',
            'orderBy.in' => 'El valor del campo Ordenar por es inválido.',
            'orderByType.required' => 'El campo Tipo de orden es obligatorio.',
            'orderByType.string' => 'El campo Tipo de orden debe ser una cadena de texto.',
            'orderByType.in' => 'El valor del campo Tipo de orden es inválido.',
            'searchByField.required' => 'El campo Búsqueda por campo es obligatorio.',
            'searchByField.string' => 'El campo Búsqueda por campo debe ser una cadena de texto.',
            'searchByField.in' => 'El valor del campo Búsqueda por campo es inválido.',
            'searchBy.string' => 'El campo Búsqueda por debe ser una cadena de texto.',
            'searchBy.max' => 'El campo Búsqueda por no debe exceder los :max caracteres.',
        ];

        // Validar la solicitud con las reglas y mensajes definidos
        $validator = Validator::make($request->all(), $rules, $messages);


        // Verificar si la validación ha fallado
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Obtén el usuario autenticado
        $user = Auth::user();
    
        // Obtén los IDs de los departamentos a los que el usuario está adscrito
        $departmentIds = $user->adscriptions->pluck('department_id');
    
        // Obtén los eventos que pertenecen a los departamentos del usuario
        $events = Event::whereIn('department_id', $departmentIds);
    
        // Aplicar filtros de ordenamiento si se han enviado
        if ($request->has('orderBy')&&$request->has('orderByType')) {
            $orderBy = $request->orderBy;
            if (!empty($orderBy)) {
                $events->orderBy($request->orderBy, $request->orderByType);
            }
        }

        // Aplicar filtros por campo si los hay
        if ($request->has('searchBy')&&$request->has('searchByField')) {
            $searchBy = $request->searchBy;
            if (!empty($searchBy)) {
                $events->where($request->searchByField,'like', '%' . $request->searchBy . '%');
            }
        }

        // Aplicar filtros por fecha de inicio si lo hay
        if ($request->has('searchByStartDate')) {
            $searchByStartDate = $request->searchByStartDate;
            if (!empty($searchByStartDate)) {
                $events->where('start_date','>=', $searchByStartDate);
            }
        }

        // Aplicar filtros por fecha de término si lo hay
        if ($request->has('searchByEndDate')) {
            $searchByEndDate = $request->searchByEndDate;
            if (!empty($searchByEndDate)) {
                $events->where('end_date','<=', $searchByEndDate);
            }
        }

        // Paginar los resultados
        $events = $events->paginate(8);

        // Convertir fechas numéricas a texto
        foreach ($events as $event) {
            $event->start_date=$this->getStringDate($event->start_date);
            $event->end_date=$this->getStringDate($event->end_date);
        }
    
        // Mantener los parámetros de filtro en el paginador
        $events->appends($request->except('page'));
    
        // Devolver la vista con los eventos
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

        // Obtiene los IDs de los departamentos a los que el usuario está adscrito
        $departmentIds = $user->adscriptions->pluck('department_id');

        // Obtiene los eventos que pertenecen a los departamentos del usuario
        $events = Event::whereIn('department_id', $departmentIds)->orderBy('start_date', 'desc')->paginate(8);
        foreach ($events as $event) {
            $event->formatted_start_date = $this->getStringDate($event->start_date);
            $event->formatted_end_date = $this->getStringDate($event->end_date);
            $event->formatted_created_at = $this->getStringDateAndHour($event->created_at); // Nuevo atributo para fecha y hora formateada
        }
        return $events;
    }

    public function show(Event $event) {
        //return $event->users;
        $event->start_date=$this->getStringDate($event->start_date);
        $event->end_date=$this->getStringDate($event->end_date);
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
    
        //$diffusionEmails = ['augarued@unam.mx', 'alejandramireles@psicologia.unam.mx', 'publicaciones.psicologia@unam.mx'];
    
        // Agregar los correos electrónicos de $areaEmails y $diffusionEmails a $emailList
        $emailList = array_merge($emailList, $areaEmails, $this->diffusionEmails);
    
        $mail = new NewEventMail($event,$emailList);
        Mail::to($responsible)->send($mail);
    }

    private function notifyRegister(Event $event) {

        if($event->space_required=='1') {
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

            
            // Agregar los correos electrónicos de $areaEmails y $diffusionEmails a $emailList
            $emailList = array_merge($emailList, $areaEmails, $this->diffusionEmails);
            $eventSpace=EventSpace::where('event_id',$event->id)->first();
            $space=Space::find($eventSpace->space_id);
            $mail=new RequestSpaceEmail($event, $space,$emailList);
            Mail::to($responsible)->send($mail);            
        }
    }

    public function preCancel(Event $event) {
        // Validaciones del movimiento
        if ($event->cancelled || $event->status !== 'finalizado') {
            return redirect()->route('dashboard')->with('error', 'Acceso ilegal, no se puede cancelar el evento');
        }
        if(!$this->validaMovimientoDepartamento($event)) {
            return redirect()->route('events.byArea')->with('error','Usted no puede realizar movimientos de otro departamento/area.');
        }

        // No se puede cancelar un evento expirado
        if($this->getEventExpired($event)) {
            return redirect()->route('events.byArea')->with('error','El evento ya está expiró, no puede cancelarse.');
        }

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

        // Validaciones del movimiento
        if ($event->cancelled || $event->status !== 'finalizado') {
            return redirect()->route('dashboard')->with('error', 'Acceso ilegal, no se puede cancelar el evento');
        }
        if(!$this->validaMovimientoDepartamento($event)) {
            return redirect()->route('events.byArea')->with('error','Usted no puede realizar movimientos de otro departamento/area.');
        }
        // No se puede cancelar un evento expirado
        if($this->getEventExpired($event)) {
            return redirect()->route('events.byArea')->with('error','El evento ya está expiró, no puede cancelarse.');
        }

        // Cancelación del evento
        $user=Auth::user();
        CanceledEvent::create([
            'event_id' => $event->id,
            'cancellation_reason' => $request->justify,
            'canceled_by_user_id' => $user->id,
        ]);

        // Actualización de cancelación en la tabla Eventos
        $event->update([
            'cancelled' => 1,
        ]);

        // Notificación de cancelación de evento a responsable y al departamento organizador
        $recipients = $this->getRecipients($event); // Obtén los destinatarios para la notificación
        Mail::to($recipients)->send(new CancelEventMail($event, $request->justify)); // Envia la notificación de cancelación a los destinatarios
        
        return redirect()->route('dashboard')->with('success','Se ha cancelado el evento correctamente');
    }

    public function byDay() {
        $user=Auth::user();
        $departmentID=$user->team->department_id;
        $allEvents = Event::select('events.*')
            ->join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('spaces.department_id', $departmentID)
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

    public function byDayAll() {
        $allEvent=true;
        $allEvents = Event::select('events.*')
            ->join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('events.status', 'finalizado')
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
        return view('events.by_day',compact('events','allEvent'));
    }

    public function selectResources(Event $event) {
        // Condición para validar evento activo y sin publicar
        if(!$this->validaEspacioActivo($event)) {
            return redirect()->route('dashboard')->with('error','No se pueden registrar requisitos a este evento');
        };        

        // Solo un gestor de eventos del departamento solicitante puede agregar los requisitos 
        if($this->validaMovimientoDepartamento($event)==false) {
            return redirect()->route('dashboard')->with('error','No puede registrar requisitos a un evento de otra área');
        }

        // Obtención de lista de recursos ya reservados y disponibles para el evento
        $reservedResources=$this->getReservedResources($event);
        $availableResources=$this->getAvailableResources($event);

        return view('events.select-resources', compact('event', 'reservedResources', 'availableResources'));
    }

    public function addResource(Event $event, Resource $resource) {
        // valida que el espacio está disponible
        if(!$this->validaEspacioActivo($event)) {
            return redirect()->route('dashboard')->with('error','No se pueden registrar requisitos a este evento');
        };

        // Solo un gestor de eventos del departamento solicitante puede agregar los requisitos 
        if($this->validaMovimientoDepartamento($event)==false) {
            return redirect()->route('dashboard')->with('error','No puede registrar requisitos a un evento de otra área');
        }

        // Pasado los filtros, se agrega el préstamo del recurso para el evento solicitado
        $eventResource=new EventResource();
        $eventResource->event_id=$event->id;
        $eventResource->resource_id=$resource->id;
        $savedResource=$eventResource->save();

        /// Obtención de lista de recursos ya reservados y disponibles para el evento
        $reservedResources=$this->getReservedResources($event);
        $availableResources=$this->getAvailableResources($event);

        // Regresando la colección de elementos para su uso en la vista
        $reservedResources = EventResource::where('event_id', $event->id)->get();

        if($savedResource) {
            return view('events.select-resources', compact('event', 'reservedResources', 'availableResources'))->with('success','Recurso solicitado correctamente.');
        } else {
            return view('events.select-resources', compact('event', 'reservedResources', 'availableResources'))->with('error','No se pudo solicitar el recurso.');
        }
    }

    public function removeResource($reservedResource, Event $event) {
        // Encuentra y elimina la tupla correspondiente en la tabla event_resources
        $removeResource=EventResource::find($reservedResource)->delete();

        // Obtención de lista de recursos ya reservados y disponibles para el evento
        $reservedResources=$this->getReservedResources($event);
        $availableResources=$this->getAvailableResources($event);

        if($removeResource) {
            return view('events.select-resources', compact('event', 'reservedResources', 'availableResources'))->with('success','Recurso eliminado correctamente.');
        } else {
            return view('events.select-resources', compact('event', 'reservedResources', 'availableResources'))->with('error','No se pudo eliminar el recurso.');
        }
    }

    private function getReservedResources (Event $event) {
        $reservedResources = EventResource::where('event_id', $event->id)->get();
        return $reservedResources;
    }

    private function getAvailableResources (Event $event) {
        // Obtener recursos reservados para el evento, se convierte en Array para identificar los elementos disponibles
        $reservedResources = EventResource::where('event_id', $event->id)
            ->pluck('resource_id')
            ->toArray();

        // Obtener recursos no reservados para el evento
        $availableResources = [];
        
        // Se hace una iteración sobre todos los espacios que se pidió para el evento, y se buscan los recursos
        foreach($event->spaces as $space) {
            foreach($space->department->resources as $resource) {
                if ($resource->active) {
                    if (!in_array($resource->id, $reservedResources)) {
                        $availableResources[] = $resource;
                    }
                } 
            }
        }
        return $availableResources;
    }

    // Validación de que un evento tiene que estar activo y sin publicar para poder agregar los recursos
    private function validaEspacioActivo(Event $event) {
        $valido=true;
        if($event->published==1||$event->cancelled==1) {
            $valido= false;
        }

        // Si el préstamo de espacio le fue rechazado, se redirige al dashboard
        if($event->space_required==1&&$valido) {
            foreach($event->eventSpaces as $eventspace) {
                if($eventspace->status=="rechazado") {
                    $valido= false;
                }
            }
        }
        return $valido;
    }
    
    // Este método valida que un usuario realice el movimiento de un área o departamento que le corresponda
    private function validaMovimientoDepartamento(Event $event) {
        $user=Auth::user();
        if($user->team!=null&&$user->team->department_id!=$event->department_id) {
            return false;
        } else {
            return true;
        }
    }

    // Obtener la lista de Responsables y Equipo de trabajo del departamento involucrado en un evento
    private function getRecipients(Event $event) {
        $recipients = [
            $event->responsible->email,
            $event->coresponsible->email,
        ];
    
        $teamEmails=[];
        $spaces=$event->spaces;
        foreach($spaces as $space) {
            $idDepartment=$space->department->id;
            $teamUsers=Team::where('department_id',$idDepartment)->get();
            foreach($teamUsers as $teamUser) {
                $userMail=User::find($teamUser->user_id);
                $teamEmails[] = $userMail->email;
            }
        }        
    
        return array_merge($recipients, $teamEmails);
    }

    // Valida si un evento ya expiró
    private function getEventExpired(Event $event) {
        $expired=false;
        if($event->start_date<=now()) {
            $expired=true;
        }
        return $expired;
    }

    // Valida si el departamento del espacio solicitado gestiona recursos
    private function isAvailableResources(Event $event) {
        $available=false;
        $spaces=$event->spaces;
        foreach($spaces as $space) {
            $resources = $space->department->resources;
            if ($resources->isNotEmpty()) {
                $available=true;
            }                
        }
        return $available;
    }

    private function getStringDate($date) {
        return Carbon::parse($date)->isoFormat('dddd D [de] MMMM [de] YYYY');
    }

    private function getStringDateAndHour($date) {
        // Establecer el idioma en español
        Carbon::setLocale('es');

        return Carbon::parse($date)->isoFormat('dddd D [de] MMMM [de] YYYY [a las] h:mm A');
    }
}
