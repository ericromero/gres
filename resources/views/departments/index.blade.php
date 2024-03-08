<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Departamentos Disponibles') }}
        </h2>
    </x-slot>


    <div class="mb-4 max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
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

        <!-- Botón para crear un nuevo departamento -->
        <a href="{{ route('departments.create') }}" class="block mb-4 text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 inline-block">
            <i class="fas fa-plus mr-2"></i>Agregar nuevo departamento
        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($departments as $department)
                <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-md rounded-lg">
                    <div class="p-1 ml-2">
                        <h3 class="text-lg font-semibold">{{ $department->name }}</h3>
                        <p>{{ $department->description }}</p>
                    </div>
                    @if($department->responsible!=null)
                    <div class="p-1 ml-2 border-t border-gray-700 dark:border-gray-300">
                        <p>Responsable: {{ $department->responsible->name }}</p>
                    </div>
                    @endif
                    <div class="p-2 flex justify-end items-center space-x-2">
                        <a href="{{ route('departments.edit', $department) }}" class="text-blue-500 dark:text-blue-300 hover:underline">Actualizar</a>
                        <a href="{{ route('department.resources', $department) }}" class="ml-2 text-green-500 dark:text-green-300 hover:underline">Recursos</a>
                        <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este departamento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Borrar</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
        </div>

    </div>

</x-app-layout>
