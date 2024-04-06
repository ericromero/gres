<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            {{ __('Espacios Disponibles') }}
        </h2>
    </x-slot>

    <div class="dark:bg-gray-800 text-gray-700 dark:text-gray-300 max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        {{-- Código para el manejo de notificaciones --}}
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-md">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="bg-red-200 text-red-800 p-4 mb-4 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <div class="py-2 border-t border-gray-700 dark:border-gray-300">
            Estimado Coordinador(a).<br>En este espacio puede consultar los diversos espacios gestionados por <b>{{$coordinatedDepartment->name}}</b>. Si requiere dar de alta un nuevo espacio o modificar la información de alguno de ellos, notifíquelo al administrador al correo ericrm@unam.mx.
        </div>

        <!-- Contenedor central -->
        <div class="grid grid-cols-1 sm:grid-cols-2 ld:grid-cols-3">
            <!-- Sección  la lista de espacios -->
            @foreach($spaces as $space)
                <div class="p-4 m-2 overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                    @if($space->availability!=1)
                        <div class="p-2 bg-red-300 text-center">ESPACIO INHABILITADO</div>
                    @endif

                    <img src="{{ asset($space->photography) }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $space->name }}</h3>
                        <p>{{ $space->description }}</p>
                    </div>

                    <div class="border-t border-gray-700 dark:border-gray-300">
                        <p>Capacidad: {{ $space->capacity }}</p>
                        <p>Ubicación: {{ $space->location }}</p>
                    </div>

                    <!-- Botón para habilitar/deshabilitar un espacio -->
                    <div class="mt-2 border-gray-700 dark:border-gray-300">
                        <h3 class="font-bold mb-2">Disponibilidad del espacio</h3>
            
                        <form action="{{ route('spaces.toggleAvailability', $space->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <label for="sumbit">Actualmente el espacio está <b>{{ $space->availability ? 'Disponible' : 'Inhabilitado' }}</b>, para cambiar el estado da clic en el siguiente botón</label>
                            <button type="submit" class="{{ $space->availability ? 'bg-red-500 text-white p-2' : 'bg-green-500 text-white p-2' }}">
                                {{ $space->availability ? 'Inhabilitar el espacio' : 'Habilitar el espacio' }}
                            </button>
                        </form>
                    </div>

                    <!-- Sección de Excepciones de Horario -->
                    <div class="p-2 m-2 border border-gray-600 dark:border-gray-200">
                        <h3 class="font-bold mb-2">Excepciones de Horario</h3>

                        {{-- Listado de Excepciones Actuales --}}
                        
                        <ul class="p-2">
                        @forelse ($space->exceptions as $exception)                
                                <li>
                                {{-- Botón para eliminar la excepción --}}
                                <form action="{{ route('spaces.exceptions.destroy', [$space->id, $exception->id]) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <label for="submit">{{ $exception->day_of_week }}: {{ $exception->start_time }} - {{ $exception->end_time }}</label>
                                    <button type="submit" class="text-red-500">Eliminar</button>
                                </form>
                                </li>
                            
                        @empty
                            <p>No hay excepciones registradas.</p>
                        @endforelse
                        </ul>

                        {{-- Formulario para Agregar Nuevas Excepciones --}}
                        <h3 class="font-semibold mb-2">Agregar excepción</h3>
                        <form action="{{ route('spaces.exceptions.store', $space->id) }}" method="POST">
                            @csrf
                            <div class="m-1">
                                <label for="day_of_week">Día de la semana:</label>
                                <select name="day_of_week" required>
                                    <option value="Lunes">Lunes</option>
                                    <option value="Martes">Martes</option>
                                    <option value="Miércoles">Miércoles</option>
                                    <option value="Jueves">Jueves</option>
                                    <option value="Viernes">Viernes</option>
                                    <option value="Sábado">Sábado</option>
                                    <option value="Domingo">Domingo</option>
                                </select>
                            </div>
                            <div class="m-1">
                                <label for="start_time">Hora de inicio:</label>
                                <input type="time" name="start_time" required>
                            
                                <label class="ml-2" for="end_time">Hora de termino:</label>
                                <input type="time" name="end_time" required>
                            </div>
                            <button type="submit" class="bg-blue-500 text-white p-2">Agregar Excepción</button>
                        </form>
                    </div>

                    
                </div>
            @endforeach


            <!-- Sección con la nueva lista de recursos -->
            <div class="p-4 m-2 overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                <h2 class="font-semibold text-lg mb-2">Recursos Actuales</h2>
                @if($resources!=null&&$resources->isNotEmpty())
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
