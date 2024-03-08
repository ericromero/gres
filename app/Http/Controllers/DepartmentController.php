<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Resource;
use App\Models\ResourceType;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::all(); // Obtener todos los departamentos desde la base de datos.

        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $institutions = Institution::all(); // Obtener todas las instituciones
        // Obtén la lista de usuarios que tienen al menos una adscripción
        $users = User::whereIn('id', function($query) {
            $query->select('user_id')
                ->from('adscriptions')
                ->orderBy('name')
                ->distinct();
        })->get();

        return view('departments.create', compact('institutions', 'users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'responsible_id' => [
                'required',
                'exists:users,id',
                Rule::unique('departments', 'responsible_id')->where(function ($query) use ($request) {
                    return $query->where('institution_id', $request->input('institution_id'));
                })
            ],
            'logo' => ['image', 'mimes:png,jpg', 'max:2048'],
        ];
    
        $messages = [
            'name.required' => 'El nombre del departamento es requerido.',
            'name.string' => 'El nombre del departamento debe ser una cadena de texto.',
            'name.max' => 'El nombre del departamento no debe exceder los 255 caracteres.',
            'description.required' => 'La descripción del departamento es requerida.',
            'description.string' => 'La descripción del departamento debe ser una cadena de texto.',
            'institution_id.required' => 'La institución es requerida.',
            'institution_id.exists' => 'La institución seleccionada no existe.',
            'responsible_id.required' => 'El responsable del departamento es requerido.',
            'responsible_id.exists' => 'El responsable seleccionado no existe.',
            'responsible_id.unique' => 'Este responsable ya está asignado a otro departamento de la misma institución.',
        ];
    
        $validatedData = $request->validate($rules, $messages);

        // Verificar si se ha enviado un nuevo logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = uniqid() . '_' . $logo->getClientOriginalName();

            // Intentar almacenar el archivo de logo en la carpeta de almacenamiento
            if (!$logo->move(public_path('images/logos'), $logoName)) {
                // Si no se puede almacenar el archivo, generar un mensaje de error y redireccionar
                return redirect()->route('departments.index')->with('error', 'No se pudo actualizar el departamento debido a un problema al cargar el logo.');
            }

            // Agregar el nombre del logo al arreglo de datos validados
            $validatedData['logo'] = $logoName;
        }
    
        Department::create($validatedData);
    
        return redirect()->route('departments.index')->with('success', 'El departamento se ha creado exitosamente.');
    }
    

    public function edit(Department $department)
    {
        $institutions = Institution::all();
        $users = User::whereIn('id', function($query) {
            $query->select('user_id')
                ->from('adscriptions')
                ->orderBy('name')
                ->distinct();
        })->get();

        return view('departments.edit', compact('department','institutions', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'institution_id' => ['required', 'exists:institutions,id'],
            'responsible_id' => [
                'required',
                'exists:users,id',
                Rule::unique('departments', 'responsible_id')->where(function ($query) use ($request, $department) {
                    return $query->where('institution_id', $request->input('institution_id'))
                                 ->where('id', '!=', $department->id);
                })
            ],
            'logo' => 'image|mimes:png,jpg|max:2048',
        ];
    
        $messages = [
            'name.required' => 'El nombre del departamento es requerido.',
            'name.string' => 'El nombre del departamento debe ser una cadena de texto.',
            'name.max' => 'El nombre del departamento no debe exceder los 255 caracteres.',
            'description.required' => 'La descripción del departamento es requerida.',
            'description.string' => 'La descripción del departamento debe ser una cadena de texto.',
            'institution_id.required' => 'La institución es requerida.',
            'institution_id.exists' => 'La institución seleccionada no existe.',
            'responsible_id.required' => 'El responsable del departamento es requerido.',
            'responsible_id.exists' => 'El responsable seleccionado no existe.',
            'responsible_id.unique' => 'Este responsable ya está asignado a otro departamento de la misma institución.',
        ];
    
        $validatedData = $request->validate($rules, $messages);
    
        // Verificar si se ha enviado un nuevo logo
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = uniqid() . '_' . $logo->getClientOriginalName();
            
            // Verificar si el nombre del nuevo logo es diferente de "unam.png"
            if ($logoName !== 'unam.png') {
                // Eliminar el logo anterior si es diferente de "unam.png"
                $previousLogoPath = public_path('images/logos/' . $department->logo);
                if (file_exists($previousLogoPath)) {
                    unlink($previousLogoPath);
                }
            }

            // Intentar almacenar el archivo de logo en la carpeta de almacenamiento
            if (!$logo->move(public_path('images/logos'), $logoName)) {
                // Si no se puede almacenar el archivo, generar un mensaje de error y redireccionar
                return redirect()->route('departments.index')->with('error', 'No se pudo actualizar el departamento debido a un problema al cargar el logo.');
            }
            $department->logo = $logoName;
        }

        // Actualizar los otros campos del departamento
        $department->name = $validatedData['name'];
        $department->description = $validatedData['description'];
        $department->institution_id = $validatedData['institution_id'];
        $department->responsible_id = $validatedData['responsible_id'];

        // Guardar los cambios en la base de datos
        $department->save();
    
        return redirect()->route('departments.index')->with('success', 'El departamento se ha actualizado exitosamente.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'El departamento se ha eliminado exitosamente.');
    }

    public function resources(Department $department) {
        $resourcetypes = ResourceType::orderBy('type')->get();
        return view('departments.resources',compact('department','resourcetypes'));
    }

    public function addResources(Request $request, Department $department) {
        // Reglas de validación
        $rules = [
            'resource_type_id' => 'required',
            'other_resource' => 'nullable|string|max:250',
            'name' => 'required|string|max:255',
            'inventory' => 'nullable|integer|min:0|unique:resources,inventory',
        ];

        // Mensajes de error personalizados
        $messages = [
            'resource_type_id.required' => 'El tipo de recurso es requerido.',
            'resource_type_id.exists' => 'El tipo de recurso seleccionado no es válido.',
            'other_resource.max' => 'El campo "Otro tipo de recurso" no debe exceder los 250 caracteres.',
            'name.required' => 'El nombre del recurso es requerido.',
            'name.string' => 'El nombre del recurso debe ser una cadena de texto.',
            'name.max' => 'El nombre del recurso no debe exceder los 255 caracteres.',
            'inventory.integer' => 'El número de inventario debe ser un número entero.',
            'inventory.min' => 'El número de inventario debe ser mayor a uno',
            'inventory.unique' => 'El número de inventario ya está registrado, debe ser único',
        ];

        // Validaciones
        $validatedData = $request->validate($rules, $messages);

        $resource_id=$request->resource_type_id;
        // Si existe Otro tipo de recurso se agrega
        if($request->resource_type_id!=null&&$request->other_resource!=null) {
            $resourceType=new ResourceType();
            $resourceType->type=$request->other_resource;
            if(!$resourceType->save()) {
                return redirect()->route('dashboard')->with('error','No se puede agregar el tipo de recurso, inténtelo nuevamente.');
            }
            $resource_id=$resourceType->id;
        }

        // Se guarda el nuevo recurso
        $resource=new Resource();
        $resource->department_id=$department->id;
        $resource->resource_type_id=$resource_id;
        $resource->name=$request->name;
        $resource->inventory=$request->inventory;
        
        if ($request->input('resource_type_id') == 'Other' && empty($request->input('other_resource'))) {
            return redirect()->route('department.resources',$department->id)->with('error', 'Por favor, ingresa el tipo de recurso si seleccionas "Otro".');
        }

        if(!$resource->save()) {
            return redirect()->route('department.resources',$department->id)->with('error','No se puede agregar el recurso, inténtelo nuevamente.');
        }        

        return redirect()->route('department.resources',$department->id)->with('success','Recurso agregado correctamente.');
    }
}
