<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\Event;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard() {
        // Eventos pendientes por atender
        $pendingEvents=null;
        $draftEvents=null;
        $unplublishEvents=null;
        $eventsArea=null;
        $user=Auth::user();
        $usuarioDepartamentoId = null;
        if(Auth::user()->team!=null) {
            $usuarioDepartamentoId=Auth::user()->team->department_id;
        }
        
        // Identificación de solicitudes pendientes
        if ($user->hasAnyRole(['Gestor de espacios', 'Coordinador'])) {
            // Solicitudes de espacio por autorizar/rechazar
            $pendingEvents = Event::join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
                ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
                ->where('spaces.department_id', $usuarioDepartamentoId)
                ->where('events.status', 'solicitado')
                ->orderBy('events.created_at','desc')
                ->select('events.*') // Seleccionar todos los campos de la tabla events
                ->get();
        }

        // Identificación de los eventos que están en borrador 
        if ($user->hasAnyRole(['Gestor de eventos', 'Coordinador'])) {
            // Eventos que no han sido publicados ni cancelados
            $draftEvents=Event::where('status','borrador')
                ->where('department_id', $usuarioDepartamentoId)
                ->orderBy('events.created_at','desc')
                ->get();
        }

        // Identificación de eventos que no han sido publicados
        if ($user->hasAnyRole(['Gestor de eventos', 'Coordinador'])) {
            // Eventos que no han sido publicados ni cancelados
            $unplublishEvents=Event::join('event_spaces','events.id','=','event_spaces.event_id')
                ->where('events.status','finalizado')
                ->where('events.published','0')
                ->where('event_spaces.status','aceptado')
                ->where('department_id', $usuarioDepartamentoId)
                ->select('events.*')
                ->orderBy('events.created_at','desc')
                ->get();
            //return $unplublishEvents;
        }

        // Identificación del número de eventos para atender por día
        if ($user->hasAnyRole(['Gestor de espacios', 'Coordinador'])) {
            $today = Carbon::today();
            $eventsArea = Event::select('events.*')
                ->join('event_spaces', 'events.id', '=', 'event_spaces.event_id')
                ->join('spaces', 'event_spaces.space_id', '=', 'spaces.id')
                ->where('spaces.department_id', $usuarioDepartamentoId)
                ->where('events.published','1')
                ->where('events.status','finalizado')
                ->where('events.cancelled','0')
                ->whereDate('events.start_date', '<=', $today)
                ->whereDate('events.end_date', '>=', $today)
                ->get();
        }


        return view('dashboard',compact('pendingEvents','draftEvents','unplublishEvents','eventsArea'));
    }

    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        return view('users.index',compact('users'));
    }

    public function create()
    {
        $departments=Department::all();
        $roles=Role::all();
        return view('users.create',compact('departments','roles'));
    }

    public function store(Request $request)
    {        
        $rules = [
            'degree' => ['required'],
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users'],
            'departments' => ['required', 'array', 'min:1'],
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',
            'departments.min' => 'Debe seleccionar al menos un departamento.',
            'departments.required' => 'Se requiere seleccionar al menos un departamento',
        ];

        $validatedData = $request->validate($rules, $messages);
    
        $email = $validatedData['email'];
        $password=Str::random(8); // Generar una contraseña aleatoria

        $user = User::create([
            'degree' => $validatedData['degree'],
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($password),
        ]);

        // Se agregan roles en caso de que existan
        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles'));
        }

        // Se agregan los departamentos seleccionados
        if (isset($validatedData['departments'])) {
            $user->departments()->sync($validatedData['departments']);
        }

        // Si tiene rol asignado, se le integra al equipo de trabajo de departamento
        if ($request->input('roles', [])!=null) {
            // Se integra al equipo de cada departamento seleccionado
            foreach ($validatedData['departments'] as $departmentId) {
                $this->integrateUserTeam($user->id, $departmentId);
            }
        }

        // Notificación por correo electrónico de la cuenta creada
        Mail::to($email)->send(new WelcomeEmail($email, $password));
    
        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function search(Request $request) {
        $user=User::find($request->user);
        return redirect()->route('users.edit',compact('user'));
    }

    public function edit(User $user)
    {
        $departments=Department::all();
        $roles=Role::all();
        return view('users.edit',compact('user','departments','roles'));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => ['required','max:255','unique:users,name,' . $user->id],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed'],
            'degree'=>['required'],
            'departments' => ['required', 'array', 'min:1'],
        ];

        $messages = [
            'name.max' => 'El nombre del usuario no debe exceder los 255 caracteres.',
            'name.required' => 'El nombre del usuario es requerido.',
            'name.unique' => 'Este nombre de usuario ya está registrado.',
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',            
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'departments.min' => 'Debe seleccionar al menos un departamento.',
            'departments.required' => 'Debe seleccionar al menos un departamento.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->degree = $validatedData['degree'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        
        $user->save();

        // Sincroniza los departamentos de adscripción
        $user->adscriptions()->delete(); // Eliminar las adscripciones existentes

        $user->adscriptions()->createMany(array_map(function ($departmentId) {
            return ['department_id' => $departmentId];
        }, $validatedData['departments']));

        // Actualizar roles del usuario
        $user->syncRoles($request->input('roles', []));

        // Si tiene rol asignado, se le integra al equipo de trabajo de departamento
        if ($request->input('roles', [])!=null) {
            // Se integra al equipo de cada departamento seleccionado
            foreach ($validatedData['departments'] as $departmentId) {
                $this->integrateUserTeam($user->id, $departmentId);
            }
        } else { // Si no tiene roles, se les quita del equipo
            $this->removeUserTeam($user->id);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        //
    }

    public function team() {
        // Obtén el departamento del usuario autenticado
        $department = Auth::user()->coordinatedDepartment;

        // Obtén la lista de usuarios que están en el mismo departamento a través de la tabla teams
        $users = User::whereHas('team', function ($query) use ($department) {
            $query->where('department_id', $department->id);
        })->get();

        return view('users.team',compact('users'));
    }

    public function storeUserTeam(Request $request)
    {        
        $rules = [
            'academic' => ['required', 'numeric','unique:teams,user_id'],
            'department' => ['required'],
            'roles'=>['required', 'array', 'min:1','in:3,4'],
        ];

        $messages = [
            'academic.required' => 'Seleccione un académico(a).',
            'academic.numeric' => 'Error al identificar al número de trabajador.',
            'academic.unique' => 'El académico(a) ya forma parte de un equipo de trabajo.',
            'department.required' => 'Se requiere seleccionar al menos un departamento.',
            'roles.required' => 'Se requiere seleccionar al menos una función del usuario.',
            'roles.in' => 'Valor inválido',
        ];

        $validatedData = $request->validate($rules, $messages);

        $user = User::find($validatedData['academic']);
        if($user==null) {
            return redirect()->route('users.team')
            ->with('error', 'El académico no existe, favor de seleccionar un académico(a) de la lista.');
        }

        // Se agregan roles en caso de que existan
        if ($request->has('roles')) {
            $user->roles()->attach($request->input('roles'));
        }

        // Crear un nuevo registro en la tabla teams
        $this->integrateUserTeam($validatedData['academic'],$validatedData['department']);
        // $usuarioAutenticado=Auth::user();
        // Team::create([
        //     'user_id' => $validatedData['academic'],
        //     'department_id' => $validatedData['department'],
        //     'register_id'=>$usuarioAutenticado->id,
        // ]);
    
        return redirect()->route('users.team')
            ->with('success', 'Usuario agregado al equipo correctamente.');
    }

    public function integrateUserTeam($academic, $department) {
        $usuarioAutenticado=Auth::user();
        // Primero se verifica si el usuario pertenece a algún departamento, lo remueve
        $userTeams=Team::where('user_id',$academic)->get();
        // Eliminar todos los equipos obtenidos
        $userTeams->each->delete();

        Team::create([
            'user_id' => $academic,
            'department_id' => $department,
            'register_id'=>$usuarioAutenticado->id,
        ]);
    }

    public function removeUserTeam($academic) {
        $usuarioAutenticado=Auth::user();
        // Primero se verifica si el usuario pertenece a algún departamento, lo remueve
        $userTeams=Team::where('user_id',$academic)->get();
        // Eliminar todos los equipos obtenidos
        $userTeams->each->delete();
    }

    public function removeTeam(Team $team)
    {
        $user = User::find($team->user_id); // Obtén el usuario asociado al equipo

        // Remueve los roles específicos del usuario
        $rolesToRemove = Role::whereIn('name', ['Gestor de eventos', 'Gestor de espacios'])->get();

        // Remueve los roles específicos del usuario
        $user->roles()->detach($rolesToRemove);

        $team->delete();
        return redirect()->back()->with('success', 'El usuario fue removido del equipo correctamente.');
    }

    public function list(Request $request) {
        $roles=Role::orderBy('name','asc')->get();
        $searchTerm = $request->input('name');
        $selectedRoles = $request->input('roles');
    
        $query = User::orderBy('name', 'asc');
    
        if (!empty($searchTerm)) {
            $query->where('name', 'like', '%' . $searchTerm . '%');
        }
    
        if (!empty($selectedRoles)) {
            $query->whereHas('roles', function ($query) use ($selectedRoles) {
                $query->whereIn('name', $selectedRoles);
            });
        }
    
        $users = $query->get();
    
        return view('users.list', compact('users','roles'));
    }
    
}
