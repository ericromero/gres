<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Espacios solicitados') }}
        </h2>
        <div class="text-gray-700 dark:text-gray-300">
            Estos son los eventos que han solicitado el uso de uno o varios espacios de la coordinación.
            <br>Da clic sobre cada miniatura para abrir en una nueva ventana el cartel del evento. 
        </div>

    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-700">
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

        <!-- Contenedor de filtros -->
        <div class="my-2">
            <button id="toggleFiltersBtn" class="bg-orange-500 px-4 py-2 text-white rounded">Filtros</button>
            <div id="filtersContainer" style="display: none;" class="border border-gray-700 p-2 m-2">
                <form action="{{ route('event_spaces.review.filter') }}" method="POST">
                    @csrf
                    <!-- Bloque de ordenamiento -->
                    <div>
                        <h3 class="font-semibold">Orden de aparición de los eventos</h3>
                        <label for="orderBy">Ordenar por:</label>
                        <select name="orderBy" id="orderBy">
                            <option value="title">Título</option>
                            <option value="start_date">Fecha</option>
                        </select>
                        <select name="orderByType" id="orderBy">
                            <option value="asc">Ascendente</option>
                            <option value="desc">Descendente</option>
                        </select>
                    </div>

                    <!-- Bloque de filtrado por campo -->
                    <div class="p-2">
                        <h3 class="font-semibold">Búsqueda por campo</h3>
                        <label for="searchByField">Criterios de búsqueda:</label>
                        <select name="searchByField" id="searchByField">
                            <option value="title">Título</option>
                            <option value="summary">Resumen</option>
                        </select>
                        
                        <input type="text" name="searchBy" id="searchBy">
                    </div>

                    <!-- Bloque de filtrado por fecha -->
                    <div class="p-2">
                        <h3 class="font-semibold">Limitar a eventos en este periodo: <span class="text-sm text-gray-600 dark:text-gray-300"> (estos dos campos son opcionales)</span></h3>
                        <label for="searchByStartDate">A partir del día</label>
                        <input type="date" name="searchByStartDate" id="searchByStartDate" />
                        <label for="searchByEndDate"> y/o a más tardar el día</label>
                        <input type="date" name="searchByEndDate" id="searchByEndDate" />
                    </div>

                    <div>
                        <button type="submit" class="bg-blue-500 px-4 py-2 text-white rounded">Aplicar filtros</button>
                        <a href="{{ route('event_spaces.review') }}" class="bg-red-500 px-4 py-2 text-white rounded">Borrar filtros</a>
                    </div>
                </form>
            </div>
        </div>
        
        @if ($events->isEmpty())
            <div class="text-center">
                <p class="text-xl font-semibold">No hay solicitudes que atender</p>
            </div>
        @else
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2 text-gray-700 dark:text-gray-200">
                @foreach ($events as $event)
                    
                    <!-- Código para saber si está rechazado -->
                    @php
                        $rechazado=false;
                        $motivo=null;
                        if ($event->space_required) {
                            foreach($event->spaces as $eventspace) {
                                $eventSpaceStatus = $eventspace->pivot->status;
                                if($eventSpaceStatus == "rechazado"&&$event->status!="borrador") {
                                    $rechazado=true;
                                    $motivo=$eventspace->pivot->observation;
                                }
                            }
                        }
                    @endphp

                    <div class="overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4 border border-gray-700 dark:border-gray-300 
                        @if($rechazado)
                            border-red-700 dark:border-red-300
                        @elseif(!$rechazado&&$event->status=="finalizado")
                            border-green-700 dark:border-green-300
                        @else
                            border-yellow-500 dark:border-yellow-300
                        @endif
                        ">
                        
                        <div class="text-center
                        @if($rechazado)
                            bg-red-700 dark:bg-red-300
                        @elseif(!$rechazado&&$event->status=="finalizado")
                            bg-green-700 dark:bg-green-300
                        @else
                            bg-yellow-600 dark:bg-yellow-300
                        @endif
                        ">
                            <h2 class="text-xl font-semibold mb-2 text-slate-100 dark:text-slate-700">{{ $event->title }}</h2>
                        </div>
                        
                        @if($event->private==1)
                            <div class="text-lg font-semibold text-amber-600 dark:text-amber-300">
                                Evento interno
                            </div>
                        @else
                            @if ($event->cover_image==null)                            
                                <img src="{{asset('images/unam.png')}}" alt="No se ha subido el cartel" class="m-2 w-20 h-20 object-cover bg-slate-300">
                                <br>No se ha subido el cartel del evento.
                            @else
                                <img src="{{asset($event->cover_image)}}" alt="{{ $event->title }}" class="w-20 h-20 object-cover cursor-pointer" onclick="window.open('{{ asset($event->cover_image) }}')">
                            @endif
                        @endif
                        
                        
                        <div class="p-4">
                            @if($event->private!=1)
                                <p class="mb-2">{{ $event->summary }}</p>
                            @endif
                            
                            <p><strong>Espacios solicitados:</strong>
                                @foreach($event->spaces as $event_space)
                                    {{$event_space->name;}}
                                @endforeach
                            </p>

                            
                            <p><strong>Departamento solicitante:</strong> {{ $event->department->name }}</p>
                            <p><strong>Persona solicitante:</strong> {{ $event->responsible->name }} (<a href="mailto:{{ $event->responsible->email }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-500 underline">{{ $event->responsible->email }}</a>)</p>
                            <p><strong>Fecha:</strong> Del {{ $event->start_date }} al {{ $event->end_date }}</p>
                            <p><strong>Horario:</strong> De {{ $event->start_time }} a {{ $event->end_time }}</p>                                                        
                            
                            @if($event->private!=1)
                                @if ($event->registration_url!=null)
                                    <p><strong>Registro:</strong><a href="{{ $event->registration_url }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-500 underline"> {{ $event->registration_url }}</a></p>
                                @else
                                    <p><strong>Registro:</strong>No se requiere</p>
                                @endif
                            
                                <!-- Información de contacto -->
                                @if ($event->contact_email!=null)
                                    <p><strong>Correo electrónico de contacto:</strong><a href="mailto:{{ $event->contact_email }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-500 underline"> {{ $event->contact_email }}</a></p>
                                @endif

                                <!-- Sitio web sobre el evento -->
                                @if ($event->website!=null)
                                    <p><strong>Sitio web del evento:</strong><a href="{{ $event->website }}" target="_blank" class="text-blue-600 hover:text-blue-900 dark:text-blue-300 dark:hover:text-blue-500 underline"> {{ $event->website }}</a></p>
                                @endif

                                <!-- Requisitos adicionales-->
                                @if ($event->requirements!=null)
                                    <p><strong>Requisitos para el evento:</strong> {{ $event->requirements }}</p>
                                @endif

                                <!-- Verificación del uso de recursos -->
                                @if($event->resources->isNotEmpty())
                                    <p><strong>Recursos/equipo solicitados:</strong></p>
                                    <ul>
                                        @foreach ($event->resources as $resource)
                                            <li class="ml-2">- {{ $resource->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            @endif

                            @if($rechazado)
                                <p><strong>Motivo de rechazo:</strong> {{ $motivo }}</p>
                            @endif


                            <!-- Botones de Autorizar y Rechazar -->
                            @if($event->status=="solicitado")
                                <div class="mt-4 flex">
                                    <div>
                                        <a href="{{ route('eventspace.authorize', $event->id) }}" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-md">Autorizar</a>
                                    </div>
                                    
                                    <div class="ml-2">
                                        <a href="{{ route('eventspace.preReject', $event->id) }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Rechazar</a>
                                    </div>                                
                                </div>
                            @endif
                        </div>
                        
                    </div>
                @endforeach
            </div>

            <div>
                {{ $events->links() }}
            </div>
            
        @endif

        <div class="items-center my-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-orange-500 text-white font-semibold rounded-md">Regresar</a>
        </div>
        
    </div>

</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleFiltersBtn = document.getElementById('toggleFiltersBtn');
        const filtersContainer = document.getElementById('filtersContainer');

        toggleFiltersBtn.addEventListener('click', function() {
            if (filtersContainer.style.display === 'none') {
                filtersContainer.style.display = 'block';
            } else {
                filtersContainer.style.display = 'none';
            }
        });
    });
</script>