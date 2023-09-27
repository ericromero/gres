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
use App\Models\Space;
use App\Models\EventType;
use App\Models\Team;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('name', 'asc')->paginate(10);
        return view('users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments=Department::all();
        $roles=Role::all();
        return view('users.create',compact('departments','roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        $rules = [
            'degree' => ['required'],
            'name' => ['required', 'string'],
            'doi' => ['required', 'numeric', 'unique:users','min:1','max:9999999'],
            'email' => ['required', 'email', 'unique:users'],
            'departments' => ['required', 'array', 'min:1'],
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',
            'doi.required' => 'El número de trabajador (DOI) es requerido.',
            'doi.numeric' => 'El número de trabajador (DOI) debe ser numérico.',
            'doi.max' => 'El número de trabajador (DOI) no puede ser mayor a 7 dígitos.',
            'departments.min' => 'Debe seleccionar al menos un departamento.',
            'departments.required' => 'Se requiere seleccionar al menos un departamento',
        ];

        $validatedData = $request->validate($rules, $messages);
    
        $email = $validatedData['email'];
        $password=Str::random(8); // Generar una contraseña aleatoria

        $user = User::create([
            'degree' => $validatedData['degree'],
            'name' => $validatedData['name'],
            'doi' => $validatedData['doi'],
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

        // Verificar si el usuario tiene el rol de Coordinador el cual tiene role_id = 2
        if (in_array('2', $request->input('roles', []))) {
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $departments=Department::all();
        $roles=Role::all();
        return view('users.edit',compact('user','departments','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed'],
            'degree'=>['required'],
            'departments' => ['required', 'array', 'min:1'],
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',            
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'departments.min' => 'Debe seleccionar al menos un departamento.',
            'departments.required' => 'Debe seleccionar al menos un departamento.',
        ];

        $validatedData = $request->validate($rules, $messages);
        $user->degree = $validatedData['degree'];
        $user->email = $validatedData['email'];

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

        // Verificar si el usuario tiene el rol de Coordinador el cual tiene role_id = 2
        if (in_array('2', $request->input('roles', []))) {
            // Se integra al equipo de cada departamento seleccionado
            foreach ($validatedData['departments'] as $departmentId) {
                $this->integrateUserTeam($user->id, $departmentId);
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }


    /**
     * Remove the specified resource from storage.
     */
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

    public function integracionEquipo() {
        
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
        Team::create([
            'user_id' => $academic,
            'department_id' => $department,
            'register_id'=>$usuarioAutenticado->id,
        ]);
    }

    // public function storeNewUserTeam(Request $request)
    // {        
    //     $rules = [
    //         'degree' => ['required'],
    //         'name' => ['required', 'string'],
    //         'doi' => ['required', 'numeric', 'unique:users','min:1','max:9999999'],
    //         'email' => ['required', 'email', 'unique:users'],
    //         'department' => ['required'],
    //         'roles'=>['required', 'array', 'min:1','in:3'],
    //     ];

    //     $messages = [
    //         'email.required' => 'El correo electrónico es requerido.',
    //         'email.email' => 'El correo electrónico debe ser una dirección válida.',
    //         'email.unique' => 'Este correo electrónico ya está en uso por otro usuario.',
    //         'doi.required' => 'El número de trabajador (DOI) es requerido.',
    //         'doi.numeric' => 'El número de trabajador (DOI) debe ser numérico.',
    //         'doi.max' => 'El número de trabajador (DOI) no puede ser mayor a 7 dígitos.',
    //         'department.required' => 'Se requiere seleccionar al menos un departamento',
    //         'roles.required' => 'Se requiere seleccionar al menos una función del usuario',
    //         'roles.in' => 'Valor inválido',
    //     ];

    //     $validatedData = $request->validate($rules, $messages);
    
    //     $email = $validatedData['email'];
    //     $password=Str::random(8); // Generar una contraseña aleatoria

    //     $user = User::create([
    //         'degree' => $validatedData['degree'],
    //         'name' => $validatedData['name'],
    //         'doi' => $validatedData['doi'],
    //         'email' => $validatedData['email'],
    //         'password' => Hash::make($password),
    //     ]);

    //     // Se agregan roles en caso de que existan
    //     if ($request->has('roles')) {
    //         $user->roles()->sync($request->input('roles'));
    //     }

    //     // Agregar un solo departamento al usuario
    //     if (isset($validatedData['department'])) {
    //         $user->departments()->attach($validatedData['department']);
    //     }

    //     // Crear un nuevo registro en la tabla teams
    //     $usuarioAutenticado=Auth::user();
    //     Team::create([
    //         'user_id' => $user->id,
    //         'department_id' => $validatedData['department'],
    //         'register_id'=>$usuarioAutenticado->id,
    //     ]);

    //     // Notificación por correo electrónico de la cuenta creada
    //     Mail::to($email)->send(new WelcomeEmail($email, $password));
    
    //     return redirect()->route('users.team')
    //         ->with('success', 'Usuario creado y agregado al equipo exitosamente.');
    // }

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


}
