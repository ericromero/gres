@component('mail::message')
{{-- Imagen --}}

![Logo]({{ asset('images/50anios.png') }})


# Solicitud de transmisión de un evento

Has recibido una solicitud para tranmitir un evento.

- Evento: {{ $event->title }}.
- Departamento: {{$event->department->name}}.
- Responsable del evento: {{$event->responsible->name;}} ({{$event->responsible->email;}})
- Periodo: del {{ $event->start_date }} al {{$event->end_date}}.
- Horario: De {{$event->start_time}} horas a {{$event->end_time}} horas.
- Lugar: {{$space->name}}

Póngase en contacto con la Coordinación o responsable solicitante para informar al respecto.

{{-- Salutation --}}
Saludos,<br>
Cartelera de eventos de la Facultad de Psicología de la UNAM.

{{-- Subcopy --}}
@component('mail::subcopy')
Si tienes problemas al hacer clic en el botón "Iniciar Sesión", copia y pega esta URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endcomponent

@endcomponent