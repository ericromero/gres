@component('mail::message')
{{-- Logo --}}
![Logo]({{ asset('images/logo-mail.png') }})

# Reporte Diario de Eventos que han registrado el uso de servicios de UDEMAT

Se registraron **{{ $eventCount }} eventos** el día de ayer que solicitaron uno o más de los siguientes servicios: grabación, transmisión o fotografía.

## Detalle de eventos:
<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; text-align: left;">
            <th style="padding: 10px; border: 1px solid #dee2e6;">Evento</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Fecha Inicio</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Fecha Fin</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Hora Inicio</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Hora Fin</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Organizador</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Email del Organizador</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Transmisión</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Grabación</th>
            <th style="padding: 10px; border: 1px solid #dee2e6;">Fotografía</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($eventData as $event)
            <tr style="background-color: {{ $loop->index % 2 == 0 ? '#ffffff' : '#f8f9fa' }};">
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    <a href="{{ url('/evento/detalle/' . $event['event_id']) }}" target="_blank" style="color: #007bff; text-decoration: none;">
                        {{ $event['name'] }}
                    </a>
                </td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['start_date'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['end_date'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['start_time'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['end_time'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['responsible_name'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['responsible_email'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['transmission_required'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['recording_required'] }}</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $event['photography_required'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Salutation --}}
Saludos,<br>
Cartelera de eventos de la Facultad de Psicología de la UNAM.

@endcomponent
