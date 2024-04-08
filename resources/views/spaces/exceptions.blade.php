<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Excepciones de horario') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($spaces as $space)
                <div class="border border-gray-900 bg-white dark:bg-gray-00 overflow-hidden shadow-md rounded-lg">
                    @if($space->availability!=1)
                        <div class="p-2 bg-red-300 text-center">ESPACIO INHABILITADO</div>
                    @endif
                    <img src="{{ asset($space->photography) }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold">{{ $space->name }}</h3>
                        <p>{{ $space->description }}</p>
                    </div>
                    <div class="p-2 ml-2 border-t dark:border-gray-700">
                        <p>Capacidad: {{ $space->capacity }}</p>
                        <p>Ubicación: {{ $space->location }}</p>
                    </div>
                

                    <!-- Sección de Excepciones de Horario -->
                    <div class="p-2 m-2 border border-gray-500 dark:border-gray-200">
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
                    </div>
                </div>
            @endforeach
        </div>

        <div class="my-4">
            <a href="{{ route('spaces.search') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
        </div>
    </div>
</x-app-layout>
