<div id="loader">
    <img src="{{ asset('images/logo-grande.png') }}" alt="Logo de Cartelera Psicología">
</div>

<style>
    /* Estilos para la pantalla de carga */
    #loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #ffffff;
        z-index: 9999;
        transition: opacity 2s ease, visibility 2s ease;
    }

    #loader.fade-out {
        opacity: 0;
        visibility: hidden;
    }

    /* Estilos para el tamaño de la imagen */
    #loader img {
        width: 50vw; /* La imagen ocupará la mitad del ancho de la pantalla en dispositivos grandes */
        max-width: 100%; /* Evita que la imagen se desborde en pantallas pequeñas */
    }

    /* Media query para dispositivos pequeños */
    @media (max-width: 768px) {
        #loader img {
            width: 100vw; /* La imagen ocupará el 100% del ancho de la pantalla en dispositivos pequeños */
        }
    }
</style>



<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cartelera') }} <a href="{{route('eventos.calendario')}}" class="text-blue-700 dark:text-blue-200 hover:underline text-sm"> (Ver calendario)</a>
        </h2>
    </x-slot>

    <div class="py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($events->isEmpty())
                <div class="text-center">
                    <p class="text-xl font-semibold">No hay eventos próximos</p>
                </div>
            @else                
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4">

                    {{-- Inicia el ciclo para publicar todos los eventos --}}
                    

                    @foreach ($events as $event)
                        <div class="{{ $event->cancelled == 1 ? 'bg-red-50' : 'bg-white' }} relative dark:bg-gray-800 border border-gray-700 dark:border-gray-100 overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4" style="min-height: 350px; max-height: 500px;">
                            @if($event->cancelled == 1)
                                <div class="text-red-700 text-center font-bold">EVENTO CANCELADO</div>
                            @endif

                            <div>
                                <a href="{{ route('events.show', ['event' => $event]) }}">
                                    <img src="{{asset($event->cover_image)}}" alt="{{ $event->title }}" class="w-full h-full object-cover rounded-t-lg">
                                </a>            
                            </div>

                            <div class="absolute bottom-0 left-0 bg-gray-700 opacity-80 text-white w-full p-4">
                                <p class="text-sm">{{ $event->date_time_text }}</p>                               

                                @if ($event->registration_url != null)
                                    <p class="text-sm"><strong>Registro:</strong> {{ $event->registration_url }}</p>
                                @endif
                            </div>

                        </div>
                    @endforeach
                
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        setTimeout(function() {
            loader.classList.add('fade-out'); 
        }, 1000); 
    });
</script>


