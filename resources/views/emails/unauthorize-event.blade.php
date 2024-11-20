@component('mail::message')
{{-- Imagen --}}

![Logo]({{ asset('images/logo-mail.png') }})

# Lo sentimos, la solicitud para uso del espacio solicitado no fue autorizada.

- Evento: {{ $event->title }}.
- Departamento: {{ $event->department->name }}
- Responsable del evento: {{ $event->responsible->name }} ({{ $event->responsible->email }})
- Periodo: del {{ $event->start_date }} al {{ $event->end_date }}.
- Horario: De {{ $event->start_time }} horas a {{ $event->end_time }} horas.
- Motivo de rechazo: {{ $reason }}

Para consultar la información sobre el evento, ingrese al sitio web Cartelera-Psicología.
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
