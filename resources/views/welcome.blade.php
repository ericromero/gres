<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cartelera') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 mb-2">
            <p>Ven a disfrutar de los diversos eventos académicos, culturales y deportivos que la Facultad de Psicología tiene para tí.</p>
            <p>Si es académica(o) de la Facultad y requieres solicitar un espacio y/o difundir su evento en este espacio, acuda al departamento o división de adscripción.</p>
        </div>
    </x-slot>

    <div class="py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($events->isEmpty())
                <div class="text-center">
                    <p class="text-xl font-semibold">No hay eventos próximos</p>
                </div>
            @else
                <div class="mb-4">
                    <a href="{{route('eventos.calendario')}}" class="text-blue-700 dark:text-blue-200 hover:underline">Ver calendario</a>
                </div>
                <div class="grid gap-4 grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4">

                    {{-- Inicia el ciclo para publicar todos los eventos --}}
                    @foreach ($events as $event)
                        <div class="{{ $event->cancelled == 1 ? 'bg-red-50' : 'bg-white' }} dark:bg-gray-800 border border-gray-700 dark:border-gray-100 overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4" style="min-width: 200px; max-width: 400px;min-height: 350px; y max-height: 500px;">
                            @if($event->cancelled == 1)
                                <div class="text-red-700 text-center font-bold">EVENTO CANCELADO</div>
                            @endif
                            {{-- <h2 class="text-xl font-semibold p-4">{{ $event->title }}</h2> --}}
                            <div class="relative" style="min-height: 300px; max-height: 500px;min-height: 350px; y max-height: 500px;">
                                <a href="{{ route('events.show', ['event' => $event]) }}">
                                    <div class="absolute inset-0 bg-cover bg-center hover:bg-opacity-80 transition duration-300" style="background-image: url('{{asset($event->cover_image)}}');">
                                    </div>
                                </a>
                                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-4">
                                    {{-- <p class="text-sm"><strong>Fecha:</strong> {{ $event->start_date }}
                                        @if($event->start_date!=$event->end_date)
                                        - {{ $event->end_date }}
                                        @endif
                                    </p>
                                    <p class="text-sm"><strong>Horario:</strong> {{ $event->start_time }} - {{ $event->end_time }}</p> --}}
                                    <p class="text-sm">{{ $event->date_time_text }}</p>

                                    {{-- <p class="text-sm"><strong>Lugar:</strong>
                                        @foreach($event->spaces as $event_space)
                                            {{$event_space->name}} ({{$event_space->location}})<br>
                                        @endforeach
                                    </p> --}}
                                    @if ($event->registration_url!=null)
                                        <p class="text-sm"><strong>Registro:</strong> {{ $event->registration_url }}</p>
                                    @else
                                        <p class="text-sm">Entrada libre.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
