<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            {{ __('Espacios Disponibles') }}
        </h2>
    </x-slot>

    <div class="dark:bg-gray-800 text-gray-700 dark:text-gray-300 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="py-2 border-t border-gray-700 dark:border-gray-300">
            Estimado Coordinador(a).<br>En este espacio puede consultar los diversos espacios gestionados por <b>{{$coordinatedDepartment->name}}</b>. Si requiere dar de alta un nuevo espacio o modificar la información de alguno de ellos, notifíquelo al administrador al correo ericrm@unam.mx.
        </div>

        <!-- Contenedor central -->
        <div class="grid grid-cols-1 sm:grid-cols-2 ld:grid-cols-3">
            <!-- Sección  la lista de epacios -->
            @foreach($spaces as $space)
                <div class="p-4 m-2 overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                    <img src="{{ asset($space->photography) }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $space->name }}</h3>
                        <p>{{ $space->description }}</p>
                    </div>
                    <div class="border-t border-gray-700 dark:border-gray-300">
                        <p>Capacidad: {{ $space->capacity }}</p>
                        <p>Ubicación: {{ $space->location }}</p>
                    </div>
                </div>
            @endforeach


            <!-- Sección con la nueva lista de recursos -->
            <div class="p-4 m-2 overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                <h2 class="font-semibold text-lg mb-2">Recursos Actuales</h2>
                @if($resources->isNotEmpty())
                    <table class="border border-gray-700 dark:border-gray-300">
                        <thead class="bg-gray-300 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">Recurso</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resources as $resource)
                                <tr>
                                    <td class="px-4 py-2 {{ $resource->active ? '' : 'text-red-700 dark:text-red-300' }}">{{ $resource->name }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('resource.toggleStatusFromCoordinator', $resource->id) }}" method="post" class="inline-block">
                                            @csrf
                                            <button type="submit" class="{{ $resource->active ? 'text-blue-800 dark:text-blue-300' : 'text-green-700 dark:text-green-300' }} hover:underline focus:outline-none">
                                                {{ $resource->active ? 'Deshabilitar' : 'Habilitar' }}
                                            </button>
                                        </form>
                                        
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No se han regitrados recursos para este departamento.</p>
                @endif
            </div>
        </div>

        <div class="p-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 ml-4 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
        </div>
    </div>
</x-app-layout>
