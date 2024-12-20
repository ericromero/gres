<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight dark:bg-gray-800 dark:text-white">
            {{ __('Búsqueda de espacio') }}
        </h2>        
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        
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

        {{-- DIV se pone el resultado de la Búsqueda --}}
        <div>
            @if(isset($availableSpaces))
                <div class="py-4">
                    <h2>A continuación se muestran los espacios disponibles, si alguno de ellos se adecúa a tus requerimientos, da clic en Seleccionar este espacio para continuar con el registro.</h2>
                    <p><b>Fecha:</b>
                        @if ($start_date==$end_date)
                            {{ $start_date_string }}
                        @else
                            del {{ $start_date_string }} al {{$end_date_string}}
                        @endif, <b>Horario:</b> De {{$start_time}} a {{$end_time}} horas.
                    </p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-2">
                
                    @if($availableSpaces->count()>0)
                        @php
                            $i=0;
                        @endphp
                        @foreach($availableSpaces as $space)
                            <div class="overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                                
                                <img src="{{ asset($space->photography) }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold">{{ $space->name }}</h3>
                                </div>
                                <div class="p-4 border-t border-gray-700 dark:border-gray-300">
                                    <p>Capacidad: {{ $space->capacity }} personas</p>
                                    <p>Ubicación: {{ $space->location }}</p>
                                </div>
                                <div class="px-4 pb-4">
                                    <form action="{{ route('events.createWithSpace') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="space" value="{{ $space->id }}">
                                        <input type="hidden" name="start_date" value="{{ $start_date }}">
                                        <input type="hidden" name="end_date" value="{{ $end_date }}">
                                        <input type="hidden" name="start_time" value="{{ $start_time }}">
                                        <input type="hidden" name="end_time" value="{{ $end_time }}">
                                        
                                        <input type="checkbox" name="private" id="private{{ $i }}">
                                        <label for="private{{ $i++ }}" class="ml-2">Evento interno o privado</label>
                                
                                        <button type="submit" class="block mb-4 mt-2 text-center px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-600 inline-block">
                                            Seleccionar este espacio
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                            No hay espacios disponibles para la fecha y hora seleccionada
                        </div>
                    @endif

                    <!-- Bloque para difundir -->
                    <div class="overflow-hidden shadow-md rounded-lg border border-gray-700 dark:border-gray-300">
                        <img src="{{ asset('images/videoconferencia.png') }}" alt="Imagen del espacio" class="w-full h-40 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold">Evento en línea <span class="text-sm">(solo difusión)</span></h3>
                        </div>
                        <div class="p-4 border-t border-gray-700 dark:border-gray-300">
                            <p>Subir información de un evento en línea para difundir en la Cartelera-Psicología.</p>
                        </div>
                        <div class="px-4 pb-4">
                            <a href="{{ route('events.createWithoutSpace', [
                                'space' => '0',
                                'start_date' => $start_date,
                                'end_date' => $end_date,
                                'start_time' => $start_time,
                                'end_time' => $end_time,
                            ]) }}" class="block mb-4 text-center px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-600 inline-block">
                                Quiero difundir mi evento
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        {{-- DIV se pone el formulario del buscador así como el calendario con eventos ya reservados --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-2">
            <div class="p-2 border border-slate-400"> <!-- Contenedor para mostrar la opción de fechas -->
                <div class="mb-2 dark:bg-gray-800 dark:text-white border-b border-gray-700 dark:border-gray-300">
                    <p>Selecciona la fecha y horario de tu evento.</p>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Permitido entre {{ $allowedStartDateText }} y {{ $allowedEndDateText}}</p>
                <form action="{{ route('spaces.search') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2">
                        
                        <div class="my-2">
                            <label for="start_date" class="text-gray-700 dark:text-gray-300 font-bold mb-2">Fecha de inicio</label>
                            <input class="dark:bg-gray-800 dark:text-white" type="date" name="start_date" min="{{ now()->addWeekdays(4)->format('Y-m-d') }}" max="{{ $allowedEndDate }}" value="{{ old('start_date') }}" required>
                        </div>
                        @error('start_date')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                        
                        <div class="my-2">
                            <label for="end_date" class="text-gray-700 dark:text-gray-300 font-bold mb-2">Fecha de fin</label>
                            <input class="dark:bg-gray-800 dark:text-white" type="date" name="end_date" min="{{ now()->addWeekdays(4)->format('Y-m-d') }}" max="{{ $allowedEndDate }}" value="{{ old('end_date') }}" required>                            
                        </div>
                        @error('end_date')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                        
                        <div class="my-2">
                            <label for="start_time" class="text-gray-700 dark:text-gray-300 font-bold mb-2">Hora de inicio</label>
                            <input class="dark:bg-gray-800 dark:text-white" type="time" name="start_time" step="1800" min="09:00" max="18:00" value="{{ old('start_time') }}" required>                            
                        </div>
                        @error('start_time')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                        
                        <div class="my-2">
                            <label for="end_time" class="text-gray-700 dark:text-gray-300 font-bold mb-2">Hora de termino</label>
                            <input class="dark:bg-gray-800 dark:text-white" type="time" name="end_time" step="1800" min="10:00" max="19:00" value="{{ old('end_time') }}" required>                            
                        </div>
                        @error('end_time')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror
                        
                        <div class="my-2">
                            <button class="block mb-4 text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 inline-block" type="submit">Buscar disponibilidad</button>
                        </div>                   
                                           
                    </div>
                </form>

                <div class="m-2">
                    <h3 class="font-bold">Excepciones de horarios</h3>
                    Recuerde que cada espacio cuenta con horarios específicos en que no pueden ser solicitados, para revisar estas excepciones de <a href="{{ route('spaces.exceptions') }}" class="text-blue-700 dark:text-blue-200 hover:underline">clic aquí</a>.
                </div>
            </div>
            
            <div class="mx-2 p-2 border border-slate-400">
                <div class="mb-2 dark:bg-gray-800 dark:text-white border-b border-gray-700 dark:border-gray-300">
                    <p><b>Disponibilidad.</b> Aquí puedes ver los espacios que han sido están solicitados y/o reservados.</p>
                </div>
                <div id="calendar">
                </div>                
            </div>
        </div>
    </div>


    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
    <script src='fullcalendar/core/locales/es.global.js'></script>
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            
          const calendarEl = document.getElementById('calendar');
          const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'es',
            initialView: 'dayGridMonth',
            events:@json($events),
            eventColor: '#551575',
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short',
            },
            eventClick: function(info) {
                // Obtén el ID del evento haciendo referencia a info.event.id
                const eventId = info.event.id;
                
                var url = "{{ url('/evento/detalle') }}/" + eventId;

                // Abre la URL en una nueva ventana
                window.open(url, '_blank');
            },

            buttonText: {
                today: 'Hoy',  // Personaliza el texto del botón 'today'
                // Puedes personalizar otros botones si lo necesitas
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
          });
          calendar.render();
        });
  
      </script>

</x-app-layout>
