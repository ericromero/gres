<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\Department;

class ResourceController extends Controller
{
    public function toggleStatus(Resource $resource) {
        $statusActive=!$resource->active;
        $resource->active=$statusActive;
        $resource->save();
        $department=Department::find($resource->department_id);
        return redirect()->route('department.resources', $department);
    }

    public function delete(Resource $resource)
    {
        try {
            // Puedes eliminar físicamente el recurso si es necesario
            $resource->delete();

            return redirect()->back()->with('success', 'Recurso desactivado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'No se pudo desactivar el recurso. Inténtelo nuevamente.');
        }
}
}
