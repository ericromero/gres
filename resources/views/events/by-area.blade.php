<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Eventos de la División/Coordinación') }}
        </h2>
        <div class="mb-2 text-gray-800 dark:text-gray-200 ">
            <p>Si el evento requiere el uso de un espacio físico, podrá publicarlo hasta que este confirmado el uso del espacio.</p>
            <p>Puede actualizar la información de un evento siempre y cuando aún no esté publicado.</p>
            <p>Una vez que el evento es publicado, aparecerá en la Cartelera y no podrá ser modificado</p>
        </div>
    </x-slot>

    <div class="py-2 max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">

        {{-- Código para el manejo de notificaciones --}}
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-md">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        

        <!-- Lista de eventos del area -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

                    <div class="overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4 border border-gray-700 dark:border-gray-300
                    
                    @if ($event->status === 'borrador')
                        bg-red-50 dark:bg-red-700 dark:text-gray-900
                    @elseif($rechazado)
                        bg-pink-50 dark:bg-pink-700 dark:text-gray-900
                    @elseif ($event->status === 'solicitado')
                        bg-yellow-50 dark:bg-yellow-700 dark:text-gray-900            
                    @elseif($event->cancelled==1)
                        bg-pink-50 dark:bg-pink-700 dark:text-gray-900
                    @elseif($event->status === 'finalizado'&&$event->published===0)
                        bg-blue-50 dark:bg-blue-700 dark:text-gray-900          
                    @elseif ( $event->published === 1)
                        bg-green-50 dark:bg-green-700 dark:text-gray-900
                    @else
                        dark:text-gray-900
                    @endif
                    ">
                        

                        @if ($event->status === 'borrador')
                            <div class="text-center text-red-700 font-bold">
                                REGISTRO EN BORRADOR
                            </div>
                        @elseif($event->status === 'solicitado')
                            <div class="text-center text-yellow-700 font-bold">
                                ESPERANDO PRESTAMO DE ESPACIO
                            </div>
                        @elseif($rechazado)
                            <div class="text-center text-pink-700 font-bold">
                                SIN PRESTAMO DE ESPACIO
                            </div>
                        @elseif($event->cancelled==1)
                            <div class="text-center text-pink-700 font-bold">
                                EVENTO CANCELADO
                            </div>
                        @elseif($event->status === 'finalizado'&&$event->published===0)
                            <div class="text-center text-blue-700 font-bold">
                                EVENTO SIN PUBLICAR
                            </div>
                        @elseif($event->published===1)
                            <div class="text-center text-green-700 font-bold">
                                ¡EVENTO PUBLICADO!
                            </div>
                        @endif

                        <img src="{{asset($event->cover_image)}}" alt="{{ $event->title }}" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">{{ $event->title }}</h2>
                            {{-- <p class="text-gray-500 dark:text-gray-300 mb-2">{{ $event->summary }}</p> --}}
                            <p><strong>Solicitante:</strong> {{ $event->responsible->name }} ({{$event->department->name}})</p>
                            <p><strong>Fecha:</strong> Del {{ $event->start_date }} al {{ $event->end_date }}</p>
                            <p><strong>Horario:</strong> De {{ $event->start_time }} a {{ $event->end_time }}</p>
                            {{-- <p><strong>Estado de solicitud:</strong> {{ $event->status }}</p>                                 --}}
                            
                            @if ($event->registration_url!=null)
                                <p><strong>Registro:</strong> {{ $event->registration_url }}</p>
                            @else
                                <p><strong>Registro:</strong> No se requiere</p>
                            @endif

                            {{-- @if ($event->program)
                             <p><a href="{{ asset($event->program) }}" class="text-blue-600 hover:text-blue-900 underline" download>Descargar Programa</a></p>
                            @endif --}}

                            <p><strong>Espacio solicitado:</strong></p>
                            @if ($event->space_required)
                                @foreach($event->spaces as $eventspace)
                                    {{$eventspace->name}} ({{$eventspace->location}})<br>
                                    {{-- Acceder al atributo "status" del espacio solicitado --}}
                                    @php
                                        $eventSpaceStatus = $eventspace->pivot->status;
                                    @endphp
                                    @if ($eventSpaceStatus == "solicitado"&&$event->status!="borrador")
                                        <p>Por favor espere mientras la(el) {{ $eventspace->department->name }} atiende su solicitud.</p>
                                    @elseif($eventSpaceStatus == "rechazado"&&$event->status!="borrador")
                                        <p><strong>Espacio rechazado:</strong> {{ $eventspace->pivot->observation }}</p>
                                    @elseif($eventSpaceStatus == "aceptado"&&$event->published==0)
                                        <p>Ahora puede publicar su evento dando clic en Publicar evento.</p>
                                    @endif
                                @endforeach
                            @else
                                No se requiere espacio
                            @endif
                            
                            @if($event->cancelled==1)
                                <div>
                                    <strong>Motivo de cancelación:</strong> {{ $event->canceledEvent->cancellation_reason }}
                                </div>
                            @endif

                            <!-- Botones de acción -->
                            <div class="mt-2 flex bg-fuchsia-50 border border-fuchsia-600">
                                <div class="mx-2">
                                    <a href="{{route('events.show',$event->id)}}" target="_blank" class="text-blue-700 hover:underline dark:text-blue-300">Detalle</a>
                                </div>
                                <!-- Solo se pueden actualizar eventos sin publicar -->
                                @if (($event->published==0&&$event->status!='finalizado')||$event->status=='solicitado')
                                    <div class="mx-2">
                                        <a href="{{route('event.edit',$event->id)}}" class="text-orange-700 hover:underline dark:text-orange-300">Actualizar</a>
                                    </div>
                                @endif

                                <!-- Solo se pueden eliminar eventos en borrador -->
                                @if ($event->status=="borrador")
                                    <!-- Botón para cancelar registro -->
                                    <div class="mx-2">
                                        <form action="{{ route('event.destroy', $event->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="text-red-700 hover:underline dark:text-orange-300" onclick="return confirm('¿Estás seguro de que deseas cancelar este registro?  Esta acción borrará del sistema la solicitud.')">
                                                {{ __('Cancelar registro') }}
                                            </button>
                                        </form>
                                    </div>
                                    {{-- <div class="mx-2">
                                        <a href="{{route('event.preDestroy',$event->id)}}" class="text-red-700 hover:underline dark:text-orange-300">Eliminar</a>
                                    </div> --}}
                                @endif

                                <!-- Solo se pueden cancelar eventos cuyo registro ha concluido -->
                                @if($event->status=='finalizado'&&$event->published==1&&$event->cancelled==0)
                                    <a href="{{route('event.preCancel',$event->id)}}" class="text-orange-700 hover:underline dark:text-red-300">Cancelar evento</a>
                                @endif

                                <!-- Se pueden publicar los eventos que tienen aceptado el espacio -->
                                @if ($event->status=="finalizado"&&$event->published==0&&!$rechazado)
                                <div class="mx-2">
                                    <a href="{{route('event.edit',$event->id)}}" class="text-orange-700 hover:underline dark:text-orange-300">Actualizar</a>
                                </div>
                                <div>
                                        <form action="{{ route('events.publish', ['id' => $event->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-green-700 hover:underline dark:text-green-300">
                                                Publicar Evento
                                            </button>
                                        </form>
                                    </div>
                                    <!-- Botón para cancelar registro -->
                                    <div class="mx-2">
                                        <form action="{{ route('event.destroy', $event->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="text-red-700 hover:underline dark:text-orange-300" onclick="return confirm('¿Estás seguro de que deseas cancelar este registro? Esta acción borrará del sistema la solicitud.')">
                                                {{ __('Cancelar registro') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

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

