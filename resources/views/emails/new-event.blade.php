@component('mail::message')
{{-- Imagen --}}

![Logo]({{ asset('images/logo-mail.png') }})

# Se ha publicado un nuevo evento en Cartelera - Psicología

- Evento: {{ $event->title }}.
- Departamento: {{ $event->department->name }}
- Responsable del evento: {{ $event->responsible->name }} ({{ $event->responsible->email }})
- Periodo: del {{ $event->start_date }} al {{ $event->end_date }}.
- Horario: De {{ $event->start_time }} horas a {{ $event->end_time }} horas.

Para consultar mayores detalles consulte el Sitio Web de la Cartelera de Psicología.
@component('mail::button', ['url' => url('/')])
Iniciar Sesión
@endcomponent

{{-- Salutation --}}
Saludos,<br>
Cartelera de eventos de la Facultad de Psicología de la UNAM.

{{-- Subcopy --}}
@component('mail::subcopy')
Si tienes problemas al hacer clic en el botón "Iniciar Sesión", copia y pega esta URL en tu navegador web: [{{ url('/') }}]({{ url('/') }})
@endcomponent

@endcomponent
