<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $event->title }}
        </h2>
    </x-slot>

    <!-- Enlaces a otras secciones -->
    <div class="max-w-7xl mx-auto pb-2 sm:px-6 lg:px-8">
        <div class="">
            <a href="{{route('eventos.cartelera')}}" class="text-blue-500 hover:underline dark:text-blue-300">Ver cartelera</a>
        </div>
        <div class="">
            <a href="{{route('eventos.calendario')}}" class="text-blue-500 hover:underline dark:text-blue-300">Ver calendario</a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto pt-2 sm:px-6 lg:px-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <!-- Primera columna: Cartel del evento -->        
        <div class="p-2 m-2 border {{ $event->cancelled=='1'? 'border-red-800 dark:border-red-300':'border-gray-700 dark:border-gray-300' }}">
            @if ($event->cancelled=='1')
                <h2 class="font-bold bg-red-700 text-slate-200 dark:bg-red-300 dark:text-slate-700 text-center text-xl mb-2">¡EVENTO CANCELADO!</h2>
            @endif

            <div>
                <h2 class="text-2xl font-bold text-center m-2">{{ $event->title }}</h2>
            </div>

            @if ($event->cover_image==null)
                <img src="{{ asset('images/unam.png') }}" alt="No se ha subido el cartel" class="w-full object-cover dark:bg-slate-300">
            @else
                <img src="{{asset($event->cover_image)}}" alt="{{ $event->title }}" class="w-full object-cover">
                <div class="flex justify-between mt-2">
                    <a href="{{ asset($event->cover_image) }}" download="{{ $event->title }}" class="text-blue-500 dark:text-blue-300">Descargar cartel</a>
                    <a href="{{ asset($event->cover_image) }}" target="_blank" class="text-blue-500 dark:text-blue-300">Abrir en ventana nueva</a>
                </div>
            @endif
            <div>
                <p><strong>Resumen</strong></p>
                <p>{{ $event->summary }}</p>
            </div>
        </div>
            
        <!-- Segunda columna: Información del evento-->
        <div class="p-6 m-2 border {{ $event->cancelled=='1'? 'border-red-800 dark:border-red-300':'border-gray-700 dark:border-gray-300' }}">
            <div>
                <h2 class="text-lg font-semibold">Información general:</h2>
            </div>

            <div class="mt-4">
                {{-- <p><strong>Responsable:</strong> {{ $event->responsible->name }}</p> --}}
                <p><strong>Fecha:</strong> {{ $event->start_date }}
                    @if($event->start_date!=$event->end_date)
                        - {{ $event->end_date }}
                    @endif
                </p>
                <p><strong>Horario:</strong> {{ $event->start_time }} - {{ $event->end_time }}</p>
                <p><strong>Lugar:</strong>
                    @foreach($event->spaces as $event_space)
                        {{$event_space->name;}} ({{$event_space->location}})
                    @endforeach
                </p>
            </div>

            @if ($event->program)
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Programa:</h2>
                    <a href="{{ asset($event->program) }}" class="text-blue-600 hover:underline dark:text-blue-300" download>Descargar Programa</a>
                </div>
            @endif                                    

            <div class="mt-4">
                <h3 class="text-lg font-semibold">Registro:</h3>
                @if ($event->registration_url!=null)
                    @if ($event->start_time > now() )
                        <a href="{{ $event->registration_url }}" class="mt-2 block text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Registrarse</a>
                    @else
                    {{ $event->registration_url }}
                    @endif
                @else
                    Entrada libre
                @endif
            </div>

            @if ($event->contact_email)
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Correo electrónico de contacto:</h2>
                    <p><a class="text-blue-900 dark:text-blue-300" href="mailto:{{$event->contact_email }}">{{$event->contact_email }}</a></p>
                </div>
            @endif 

            @if ($event->website)
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Sitio web del evento:</h2>
                    <p><a class="text-blue-900 dark:text-blue-300" href="{{$event->website }}" target="_blank">{{ $event->website }}</a></p>
                </div>
            @endif

            @if ($event->requirements)
                <div class="mt-4">
                    <h2 class="text-lg font-semibold">Requisitos adicionales:</h2>
                    <p>{{ $event->requirements }}</p>
                </div>
            @endif 

        </div>
        
        
        <!-- Recursos solicitados y Lista de participantes -->
        <div class="p-6 m-2 border {{ $event->cancelled=='1'? 'border-red-800 dark:border-red-300':'border-gray-700 dark:border-gray-300' }}">
            <!-- Lista de recursos -->
            <div class="m-2">
                <h3 class="text-lg font-semibold">Recursos/equipo solicitados</h3>
                @if($event->resources->isNotEmpty())
                                <ul>
                                    @foreach ($event->resources as $resource)
                                        <li class="ml-2">- {{ $resource->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
            </div>
            <!-- Lista de participantes -->
            <div class="m-2">
                <h3 class="text-lg font-semibold">Participantes</h3>
                @if ($participants!=null&&$participants->count() > 0)
                    <table class="border border-gray-700 dark:border-gray-300">
                        <thead class="bg-gray-300 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">Nombre completo</th>
                                <th class="px-4 py-2">Tipo de participación</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($participants as $participant)
                                <tr>
                                    <td class="px-4 py-2">
                                        @if($participant->user_id!=null)
                                            {{ $participant->user->degree }}
                                        @endif 
                                        {{ $participant->fullname }}
                                    </td>
                                    <td class="px-4 py-2">{{ $participant->participationType->name }}</td>
                                    
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay participantes agregados al evento.</p>
                @endif
            </div>
            
        </div>
    </div>

    <div class="ml-10 my-4">
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-orange-500 text-white font-semibold rounded-md">Regresar</a>
    </div>
</x-app-layout>
