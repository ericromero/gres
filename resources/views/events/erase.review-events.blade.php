<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Revisar Eventos') }}
        </h2>
        <div class="text-gray-600 mb-2">
            <p class="text-gray-900 dark:text-gray-100">
                En esta sección, puedes ver todos los eventos que requieren ser validados para continuar su proceso de reserva de espacio y/o publicación en cartelera.
                Si alguno de los eventos es rechazado es necesario que indiques en el recuadro correspondientes los motivos de ello.
            </p>
        </div>
    </x-slot>

    <div class="py-2">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Código para el manejo de notificaciones --}}
            @if(session('success'))
                <div class="bg-green-200 text-green-800 p-4 mb-4 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($events->isEmpty())
                        <p>No hay eventos pendientes de revisión.</p>
                    @else
                        <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2">Título</th>
                                    <th class="px-4 py-2">Usuario</th>
                                    <th class="px-4 py-2">Departamento</th>
                                    <th class="px-4 py-2">Fecha de Solicitud</th>
                                    <th class="px-4 py-2">Motivos de Rechazo</th>
                                    <th class="px-4 py-2">Dictaminación</th> 
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($events as $event)
                                    <tr>
                                        <td class="border px-4 py-2">{{ $event->title }}</td>
                                        <td class="border px-4 py-2">{{ $event->user->name ?? 'Usuario no disponible' }}</td>
                                        <td class="border px-4 py-2">{{ $event->user->department->name ?? 'Departamento no disponible' }}</td>
                                        <td class="border px-4 py-2">{{ $event->created_at->format('Y-m-d') }}</td>
                                        <!-- Formulario para actualizar el estatus del evento -->
                                        <form action="{{ route('events.validar', $event) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <td class="border px-4 py-2">
                                                <textarea name="cancellation_reason" class="form-input" placeholder="Llena este espacio solo en caso de rechazar el evento" ></textarea>
                                            </td>
                                            <td class="border px-4 py-2">
                                                
                                                    <input type="hidden" name="id" value="{{ $event->id }}">
                                                    <select name="status" class="form-select" required>
                                                        <option selected>Seleccione una opcion</option>
                                                        <option value="aceptado" {{ $event->status === 'accepted' ? 'selected' : '' }}>Aceptado</option>
                                                        <option value="rechazado" {{ $event->status === 'rejected' ? 'selected' : '' }}>Rechazado</option>
                                                    </select>
                                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Actualizar</button>
                                                
                                            </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

