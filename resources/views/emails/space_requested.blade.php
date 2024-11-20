@component('mail::message')
{{-- Imagen --}}

![Logo]({{ asset('images/logo-mail.png') }})


# Solicitud de Espacio

Has recibido una solicitud para reservar un espacio.

- Espacio solicitado: {{$space->name}}.
- Evento: {{ $event->title }}.
- Departamento: {{$event->department->name}}.
- Responsable del evento: {{$event->responsible->name;}} ({{$event->responsible->email;}})
- Periodo: del {{ $event->start_date }} al {{$event->end_date}}.
- Horario: De {{$event->start_time}} horas a {{$event->end_time}} horas.

Para acceder al sistema, da clic en el siguiente botón.
@component('mail::button', ['url' => url('/login')])
Iniciar Sesión
@endcomponent

{{-- Salutation --}}
Saludos,<br>
Cartelera de eventos de la Facultad de Psicología de la UNAM.

{{-- Subcopy --}}
@component('mail::subcopy')
Si tienes problemas al hacer clic en el botón "Iniciar Sesión", copia y pega esta URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endcomponent

@endcomponent