<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight dark:bg-gray-800 dark:text-white">
            {{ __('Eventos por día') }}
            <span class="text-sm text-gray-700 dark:text-gray-300">
                @if (isset($allEvent)&&$allEvent!=null)
                    (Está usted viendo todos los eventos)
                @else
                    (Está usted viendo solo los eventos de su área)
                @endif
            </span>
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        
        <!-- Botón para intercambiar de ver eventos del área o todos los eventos -->
        <div class="my-4">
            @if (isset($allEvent)&&$allEvent!=null)
                <a href="{{ route('events.byDay') }}" class="px-4 py-2 text-blue-700 hover:text-blue-800 hover:underline font-semibold rounded-md">Ver solo eventos del área</a>
            @else
                <a href="{{ route('events.byDayAll') }}" class="px-4 py-2 text-blue-700 hover:text-blue-800 hover:underline font-semibold">Ver todos los eventos</a>
            @endif
        </div>
        
        <!-- Formulario del buscador así como el calendario con eventos ya reservados -->
        <div class="P-4">
                       
            <div class="mx-2 p-2 border border-slate-400">
                <div class="mb-2 dark:bg-gray-800 dark:text-white border-b border-gray-700 dark:border-gray-300">
                    <p>Aquí puedes ver los eventos ya publicados.</p>
                </div>
                <div id="calendar">
                </div>                
            </div>
        </div>

        <!-- Botón de regreso -->
        <div class="items-center my-4 ml-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-orange-500 text-white font-semibold rounded-md">Regresar</a>
        </div>
    </div>

    


<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script src='fullcalendar/core/locales/es.global.js'></script>
<script>
    
    document.addEventListener('DOMContentLoaded', function() {
        
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'es',
        initialView: 'timeGridWeek',
        nowIndicator: true,
        today:    'Día de hoy',
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
            window.open(url, '_top');
        },

        buttonText: {
            today: 'Hoy',
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
