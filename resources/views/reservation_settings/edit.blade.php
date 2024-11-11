<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Configuración')}}
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
        
        <div>
            <form action="{{ route('reservation_settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group my-2">
                    <label for="start_date">Fecha de Inicio de Reservas</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $settings->start_date }}" required>
                </div>

                <div class="form-group">
                    <label for="end_date">Fecha de Fin de Reservas</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $settings->end_date }}" required>
                </div>

                <button type="submit" class="block my-2 text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 inline-block">Guardar Configuración</button>
            </form>
        </div>

        <div class="my-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-500 text-white rounded-md">Regresar</a>
        </div>

</div>
</x-app-layout>