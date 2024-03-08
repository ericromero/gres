<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cartelera') }}
        </h2>
        <div class="text-gray-600 mb-2  dark:text-gray-100"">
            <p>Ven a disfrutar de los diversos eventos académicos, culturales y deportivos que la Facultad de Psicología tiene para tí.</p>
        </div>
    </x-slot>

    <div class="py-2  dark:text-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">            
            <div class="mb-4">
                <a href="{{route('eventos.cartelera')}}" class="text-blue-700 dark:text-blue-200 hover:underline">Ver cartelera</a>
            </div>

            <div id="calendar">
                
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

            buttonText: {
                today: 'Hoy',  // Personaliza el texto del botón 'today'
                // Puedes personalizar otros botones si lo necesitas
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },

            eventClick: function(info) {
                // Obtén el ID del evento haciendo referencia a info.event.id
                const eventId = info.event.id;
                
                // Redirige a la página de detalles del evento usando la ruta con el ID
                window.location.href = "{{ url('/evento/detalle') }}/" + eventId;
            },
          });
          calendar.render();
        });
  
      </script>

</x-app-layout>
