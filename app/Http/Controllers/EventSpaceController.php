<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EventSpace;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Mail\UnauthorizeEventMail;
use App\Mail\authorizeEventMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class EventSpaceController extends Controller
{
    public function index() {
        // Paso 1: Obtener el ID del departamento del usuario autenticado
        $usuarioDepartamentoId = Auth::user()->team->department_id;        

        // Paso 2: Obtener todos los eventos solicitados del departamento del usuario
        $events = Event::join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('spaces.department_id', $usuarioDepartamentoId)
            ->whereNot('events.status', 'borrador')
            ->select('events.*') // Seleccionar todos los campos de la tabla events
            ->paginate(8);

            // Convertir fechas numéricas a texto
        foreach ($events as $event) {
            $event->start_date=$this->getStringDate($event->start_date);
            $event->end_date=$this->getStringDate($event->end_date);
        }

        return view('eventspaces.index',compact('events'));
    }

    public function indexFilter(Request $request) {
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

        // Paso 1: Obtener el ID del departamento del usuario autenticado
        $usuarioDepartamentoId = Auth::user()->team->department_id;

        // Paso 2: Obtener todos los eventos solicitados del departamento del usuario
        $events = Event::join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('spaces.department_id', $usuarioDepartamentoId)
            ->whereNot('events.status', 'borrador')
            ->select('events.*');

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
        return view('eventspaces.index', compact('events'));
    }

    public function awaitingRequests() {

        // Paso 1: Obtener el ID del departamento del usuario autenticado
        $usuarioDepartamentoId = Auth::user()->team->department_id;        

        // Paso 2: Obtener todos los eventos solicitados del departamento del usuario
        $events = Event::join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
            ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
            ->where('spaces.department_id', $usuarioDepartamentoId)
            ->where('events.status', 'solicitado')
            ->select('events.*') // Seleccionar todos los campos de la tabla events
            ->paginate(8);
        
        // Convertir fechas numéricas a texto
        foreach ($events as $event) {
            $event->start_date=$this->getStringDate($event->start_date);
            $event->end_date=$this->getStringDate($event->end_date);
        }

        return view('eventspaces.index',compact('events'));
    }

    public function authorizeRequestSpace(Event $event)
    {
        // Verificando que la solicitud esté pendiente
        if($event->status!='solicitado') {
            return redirect()->route('event_spaces.review')->with('error', 'Acceso ilegal, la solicitud ha habia sido atendida.');
        }

        $user=Auth::user();        
        $eventSpace = EventSpace::where('event_id', $event->id)->first();
        $eventSpace->status = 'aceptado';
        $eventSpace->validate_by=$user->id;
        $eventSpace->save();

        $event->status='finalizado';
        $event->save();

        // Bloque para envío de notificación
        $responsible = User::find($event->responsible_id);
        $userDepartmentId = $user->team->department_id;

        // Obtener la lista de correos electrónicos de usuarios en el mismo departamento
        $emailList = User::join('teams', 'users.id', '=', 'teams.user_id')
            ->where('teams.department_id', $userDepartmentId)
            ->pluck('email');

        // Se notifica al responsable sobre el rechazo del préstamo
        $mail = new authorizeEventMail($event,$emailList);
        Mail::to($responsible)->send($mail);

        return redirect()->route('event_spaces.review')->with('success', 'La solicitud ha sido autorizada.');
    }

    public function preRejectRequestSpace(Event $event) {
        // Verificando que la solicitud esté pendiente
        if($event->status!='solicitado') {
            return redirect()->route('event_spaces.review')->with('error', 'Acceso ilegal, la solicitud ha habia sido atendida.');
        }

        // Convertir fechas numéricas a texto
        $event->start_date=$this->getStringDate($event->start_date);
        $event->end_date=$this->getStringDate($event->end_date);

        return view('eventspaces.reject',compact('event'));
    }

    public function rejectRequestSpace(Request $request)
    {       
        $request->validate([
            'observation' => 'required|min:100|max:2000',
        ], [
            'observation.required' => 'El campo observación es obligatorio.',
            'observation.min' => 'El campo observación debe tener al menos :min caracteres.',
            'observation.max' => 'El campo observación no puede tener más de :max caracteres.',
        ]);

        $user=Auth::user();
        $event=Event::find($request->event);

        // Verificando que la solicitud esté pendiente
        if($event->status!='solicitado') {
            return redirect()->route('event_spaces.review')->with('error', 'Acceso ilegal, la solicitud ha habia sido atendida.');
        }
        
        $reason=$request->input('observation');
        
        $eventSpace = EventSpace::where('event_id', $event->id)->first();
        $eventSpace->status = 'rechazado';
        $eventSpace->observation =$reason;  
        $eventSpace->validate_by=$user->id;
        $eventSpace->save();

        $event->status='finalizado';
        $event->save();

        // Bloque para envío de notificación
        $responsible = User::find($event->responsible_id);
        $userDepartmentId = $user->team->department_id;

        // Obtener la lista de correos electrónicos de usuarios en el mismo departamento
        $emailList = User::join('teams', 'users.id', '=', 'teams.user_id')
            ->where('teams.department_id', $userDepartmentId)
            ->pluck('email');

        // Se notifica al responsable sobre el rechazo del préstamo
        $mail = new UnauthorizeEventMail($event,$reason,$emailList);
        Mail::to($responsible)->send($mail);        

        return redirect()->route('event_spaces.review')->with('success', 'La solicitud ha sido rechazada.');
    }

    private function getStringDate($date) {
        return Carbon::parse($date)->isoFormat('dddd D [de] MMMM [de] YYYY');
    }

}
