<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lineamientos para el uso de espacios') }}
        </h2>
    </x-slot>

    <div class="p-6">
        <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($spaces as $space)
            <div class="bg-white dark:bg-gray-700 overflow-hidden shadow-md rounded-lg">
                @if($space->availability!=1)
                    <div class="p-2 font-bold bg-red-300 text-center dark:bg-red-700">ESPACIO INHABILITADO</div>
                @endif
                <img src="{{ asset($space->photography) }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                <div class="p-4">
                    <h3 class="text-lg font-semibold">{{ $space->name }}</h3>
                </div>
                <div class="p-2 ml-2 border-t dark:border-gray-700">
                
                </div>

                @if(isset($space->terms)&&$space->terms!=null)
                    <a class="p-2" href="{{ $space->terms }}" target="_blank">Ver lineamientos</a>
                @else
                    <div class="p-2 text-red-600 dark:text-red-300">No se ha cargado el documento de lineamientos.</div>
                @endif
                
            </div>
                
            @empty
                <div>No hay ningún espacio registrado.</div>
            @endforelse

        </div>
    </div>
</x-app-layout>

    