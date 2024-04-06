<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Eventos de la División/Coordinación') }}
        </h2>
        <div class="text-gray-800 dark:text-gray-200 ">
            <p>Si el evento requiere el uso de un espacio físico, podrá publicarlo hasta que este confirmado el uso del espacio.</p>
            <p>Puede actualizar la información de un evento siempre y cuando aún no esté publicado.</p>
            <p>Una vez que el evento es publicado, aparecerá en la Cartelera y no podrá ser modificado</p>
        </div>
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

        <!-- Contenedor de filtros -->
        <div class="my-2">
            <button id="toggleFiltersBtn" class="bg-orange-500 px-4 py-2 text-white rounded">Filtros</button>
            <div id="filtersContainer" style="display: none;">
                <form action="{{ route('events.byArea.filter') }}" method="POST">
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
                        <a href="{{ route('events.byArea') }}" class="bg-red-500 px-4 py-2 text-white rounded">Borrar filtros</a>
                    </div>
                </form>
            </div>
        </div>
        

        <!-- Lista de eventos del area -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if (!empty($events) && count($events) > 0)
                @foreach ($events as $event)

                    <!-- Código para saber si está rechazado -->
                    @php
                        $rechazado=false;
                        if ($event->space_required) {
                            foreach($event->spaces as $eventspace) {
                                $eventSpaceStatus = $eventspace->pivot->status;
                                if($eventSpaceStatus == "rechazado"&&$event->status!="borrador") {
                                    $rechazado=true;
                                }
                            }
                        }
                    @endphp

                    <!-- Contenedor de un solo evento -->
                    <div class="overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4 border border-gray-700 dark:bg-gray-800 dark:text-gray-100
                        @if ($event->status === 'borrador')
                            border-red-700 dark:border-red-300 
                        @elseif($rechazado)
                            border-pink-700 dark:border-pink-300 
                        @elseif ($event->status === 'solicitado')
                            border-yellow-700  dark:border-yellow-300            
                        @elseif($event->cancelled==1)
                            border-pink-700 dark:border-pink-300 
                        @elseif($event->status === 'finalizado'&&$event->published===0)
                            border-blue-700 dark:border-blue-300          
                        @elseif ( $event->published === 1)
                            border-green-700 dark:border-green-300
                        @else
                            dark:text-gray-900
                        @endif
                    ">
                        

                        @if ($event->status === 'borrador')
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-red-700 dark:bg-red-200 font-bold">
                                REGISTRO EN BORRADOR
                            </div>
                        @elseif($event->status === 'solicitado')
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-yellow-600 dark:bg-yellow-200 font-bold">
                                ESPERANDO PRESTAMO DE ESPACIO
                            </div>
                        @elseif($rechazado)
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-pink-700 dark:bg-pink-200 font-bold">
                                SIN PRESTAMO DE ESPACIO
                            </div>
                        @elseif($event->cancelled==1)
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-red-700 dark:bg-pink-200 font-bold">
                                EVENTO CANCELADO
                            </div>
                        @elseif($event->status === 'finalizado'&&$event->published===0)
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-blue-700 dark:bg-blue-200 font-bold">
                                EVENTO SIN PUBLICAR
                            </div>
                        @elseif($event->published===1)
                            <div class="text-center text-slate-100 dark:text-slate-700 bg-green-700 dark:bg-green-200 font-bold">
                                ¡EVENTO PUBLICADO!
                            </div>
                        @endif

                        <!-- Título de evento -->
                        <div class="ml-d text-center">                            
                            <h2 class="text-xl font-semibold mb-2">{{ $event->title }}</h2>
                        </div>

                        <!-- Imagen del evento en minuatura -->
                        <div class="ml-4">
                            @if ($event->cover_image == null)                            
                                <img src="{{ asset('images/unam.png') }}" alt="{{ $event->title }}" class="w-20 h-20 object-cover dark:bg-slate-300">
                            @else
                                <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" class="w-20 h-20 object-cover dark:bg-slate-300 cursor-pointer" onclick="window.open('{{ asset($event->cover_image) }}')">
                            @endif
                        </div>

                        <!-- Sección de información-->
                        <div class="p-4">
                            <p><strong>Departamento solicitante:</strong>  {{$event->department->name}}</p>
                            <p><strong>Persona responable del event:</strong> {{ $event->responsible->name }} <a href="mailto:{{ $event->responsible->email }}" class="text-blue-800 dark:text-blue-200"> {{ $event->responsible->email }}</a></p>
                            <p><strong>Fecha:</strong> Del {{ $event->start_date }} al {{ $event->end_date }}</p>
                            <p><strong>Horario:</strong> De {{ $event->start_time }} a {{ $event->end_time }}</p>
                            
                            @if ($event->registration_url!=null)
                                <p><strong>Registro:</strong> {{ $event->registration_url }}</p>
                            @else
                                <p><strong>Registro:</strong> No se requiere</p>
                            @endif

                            {{-- @if ($event->program)
                             <p><a href="{{ asset($event->program) }}" class="text-blue-600 hover:text-blue-900 underline" download>Descargar Programa</a></p>
                            @endif --}}

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

                            <p><strong>Espacio solicitado:</strong></p>
                            @if ($event->space_required)
                                @foreach($event->spaces as $eventspace)
                                    {{$eventspace->name}} ({{$eventspace->location}})<br>
                                    {{-- Acceder al atributo "status" del espacio solicitado --}}
                                    @php
                                        $eventSpaceStatus = $eventspace->pivot->status;
                                    @endphp
                                    <p class="border border-gray-700 p-2 mt-2">
                                        @if ($eventSpaceStatus == "solicitado"&&$event->status!="borrador")
                                            Por favor espere mientras la(el) {{ $eventspace->department->name }} atiende su solicitud.
                                        @elseif($eventSpaceStatus == "rechazado"&&$event->status!="borrador")
                                            <strong>Espacio rechazado:</strong> {{ $eventspace->pivot->observation }}
                                        @elseif($eventSpaceStatus == "aceptado"&&$event->published==0)
                                            Ahora puede publicar su evento dando clic en Publicar evento.
                                    @endif
                                    </p>
                                @endforeach
                            @else
                                No se requiere espacio
                            @endif
                            
                            @if($event->cancelled==1)
                                <div>
                                    <strong>Motivo de cancelación:</strong> {{ $event->canceledEvent->cancellation_reason }}
                                </div>
                            @endif

                            
                        </div>

                        <!-- Botones de acción -->
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 bg-fuchsia-50 border border-fuchsia-600 dark:bg-slate-700 dark:border-fuchsia-200">
                            <!-- Detalle -->
                            <div class="mx-2">
                                <a href="{{route('events.show',$event->id)}}" class="text-blue-700 hover:underline dark:text-blue-300">Detalle</a>
                            </div>

                            <!-- Eventos solicitado y en espera de autorización -->
                            {{-- @if (($event->published==0&&$event->status!='finalizado')||$event->status=='solicitado') --}}
                            @if ($event->status=='solicitado'&&$event->cancelled==0)
                                <!-- Actualizar -->
                                <div class="mx-2">
                                    <a href="{{route('event.edit',$event->id)}}" class="text-orange-700 hover:underline dark:text-orange-300">Actualizar</a>
                                </div>
                                <!-- Cancelar registro -->
                                <div class="mx-2">
                                    <form action="{{ route('event.destroy', $event->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        
                                        <button type="submit" class="text-red-700 hover:underline dark:text-orange-300" onclick="return confirm('¿Estás seguro de que deseas cancelar este registro?  Esta acción borrará del sistema la solicitud.')">
                                            {{ __('Cancelar registro') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Eventos en borrador -->
                            @if ($event->status=="borrador"&&$event->cancelled==0)
                                <!-- Actualizar -->
                                <div class="mx-2">
                                    <a href="{{route('event.edit',$event->id)}}" class="text-orange-700 hover:underline dark:text-orange-300">Actualizar</a>
                                </div>
                                <!-- Cancelar registro -->
                                <div class="mx-2">
                                    <form action="{{ route('event.destroy', $event->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        
                                        <button type="submit" class="text-red-700 hover:underline dark:text-red-300" onclick="return confirm('¿Estás seguro de que deseas cancelar este registro?  Esta acción borrará del sistema la solicitud.')">
                                            {{ __('Cancelar registro') }}
                                        </button>
                                    </form>
                                </div>
                            @endif

                            <!-- Evento publicado -->
                            @if($event->status=='finalizado'&&$event->published==1&&$event->cancelled==0)
                                <!-- Cancelar evento -->
                                <div class="mx-2">
                                    <a href="{{route('event.preCancel',$event->id)}}" class="text-orange-700 hover:underline dark:text-red-300">Cancelar evento</a>
                                </div>
                            @endif

                            <!-- Evento con espacio aceptado y sin publicar -->
                            @if ($event->status=="finalizado"&&$event->published==0&&!$rechazado&&$event->cancelled==0)
                                
                                <!-- Actualizar -->
                                <div class="mx-2">
                                    <a href="{{route('event.edit',$event->id)}}" class="text-orange-700 hover:underline dark:text-orange-300">Actualizar</a>
                                </div>

                                <!-- Publicar -->
                                <div class="mx-2">
                                    <form action="{{ route('events.publish', ['id' => $event->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-green-700 hover:underline dark:text-green-300">
                                            Publicar Evento
                                        </button>
                                    </form>
                                </div>

                                <!-- Cancelar evento -->
                                <div class="mx-2">
                                    <a href="{{route('event.preCancel',$event->id)}}" class="text-orange-700 hover:underline dark:text-red-300">Cancelar evento</a>
                                </div>
                            @endif
                        </div>
                        
                    </div>
                @endforeach
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow-sm sm:rounded-lg p-4 text-center">
                    <p class="text-gray-500 mb-2">No tienes eventos registrados.</p>
                </div>
            @endif
        </div>

        <div>
            {{ $events->links() }}
        </div>

        <div class="items-center my-4 ml-4">
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
