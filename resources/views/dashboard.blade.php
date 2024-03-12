<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestor de Recursos, Espacios y Servicios ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                
                <!-- Imagen y enlace para gestionar roles -->
                {{-- @hasrole('Administrador')
                    <a href="{{ route('roles.index') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer">
                            <img src="{{ asset('images/permisos.png') }}" alt="Permisos" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Roles</p>
                        </div>
                    </a>
                @endhasrole --}}
                

                <!-- Imagen y enlace para gestionar permisos -->
                {{-- @hasrole('Administrador')
                    <a href="{{ route('permissions.index') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer">
                            <img src="{{ asset('images/permisos.png') }}" alt="Permisos" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Permisos</p>
                        </div>
                    </a>
                @endhasrole --}}

                <!-- Imagen y enlace para gestionar usuarios -->
                @hasrole('Administrador')
                    <a href="{{ route('users.index') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/usuarios.png') }}" alt="Usuarios" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Usuarios</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para gestionar departamentos -->
                @hasrole('Administrador')
                    <a href="{{ route('departments.index') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/departamento.png') }}" alt="Departamentos" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Departamentos</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para gestionar espacios -->
                @hasrole('Administrador')
                    <a href="{{ route('spaces.index') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/espacios.png') }}" alt="Espacios" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Espacios</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para consultar espacios -->
                @hasanyrole('Coordinador')
                    <a href="{{ route('spaces.my-spaces') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/espacios.png') }}" alt="Mis espacios y recursos" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Mis espacios y recursos</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para contruir el equipo de trabajo -->
                @hasanyrole('Coordinador')
                    <a href="{{ route('users.team') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/equipo.png') }}" alt="Equipo de trabajo" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Equipo de trabajo</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para revisar la agenda de día -->
                @hasanyrole('Coordinador|Gestor de eventos')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                        <a href="{{ route('events.byDay') }}">
                                <img src="{{ asset('images/evento.png') }}" alt="Solicitar espacio" class="mx-auto h-20">
                                <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Eventos del día</p>
                        </a>

                        <!-- Notificación de eventos por atender al día -->
                        @if ($eventsArea!=null&&$eventsArea->count()==1)
                            <a href="{{ route('events.byDay') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                Hoy hay 1 evento.
                            </a>
                        @elseif ($eventsArea!=null&&$eventsArea->count()>1)
                            <a href="{{ route('events.byDay') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                Hoy hay {{$eventsArea->count()}} eventos.
                            </a>
                        @endif

                    </div>
                @endhasrole

                <!-- Imagen y enlace para crear un nuevo evento -->
                @hasanyrole('Coordinador|Gestor de eventos')
                    <a href="{{ route('spaces.search') }}">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <img src="{{ asset('images/calendario.png') }}" alt="Solicitar espacio" class="mx-auto h-20">
                            <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Registrar evento</p>
                        </div>
                    </a>
                @endhasrole

                <!-- Imagen y enlace para revisar los eventos solicitados por la coordinación -->
                @hasanyrole('Coordinador|Gestor de eventos')
                    
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                            <a href="{{ route('events.byArea') }}">
                                <img src="{{ asset('images/autorizacioneventos.png') }}" alt="Autorización de eventos" class="mx-auto h-20">
                                <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Eventos de la coordinación</p>
                            </a>

                            <!-- Notificación de eventos en borrador -->
                            @if ($draftEvents->count()==1)
                                <a href="{{ route('events.byArea.drafts') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay un evento sin registrar.
                                </a>
                            @elseif ($draftEvents->count()>1)
                                <a href="{{ route('events.byArea.drafts') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay {{ $draftEvents->count() }} eventos sin registrar.
                                </a>
                            @endif

                            <!-- Notificación de eventos aceptado y no publicados -->                            
                            @if ($unplublishEvents->count()==1)
                                <a href="{{ route('events.byArea.unPublish') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay un evento sin publicar.
                                </a>
                            @elseif ($unplublishEvents->count()>1)
                                <a href="{{ route('events.byArea.unPublish') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay {{ $unplublishEvents->count() }} eventos sin publicar.
                                </a>
                            @endif

                        </div>
                    
                @endhasrole
                
                <!-- Imagen y enlace a los espacios solicitados por la coordinación -->
                @hasrole('Gestor de espacios')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                        <a href="{{ route('event_spaces.review') }}">
                            <img src="{{ asset('images/espacios_solicitados.png') }}" alt="Mis eventos" class="mx-auto h-20">
                            <p class="block text-center mt-2 text-gray-900 dark:text-gray-100">Espacios solicitados</p>
                        </a>

                        <!-- Notificación de espacios solicitados sin atender -->
                        @if ($pendingEvents->count()==1)
                            <a href="{{ route('event_spaces.awaitingRequests') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                Hay una solicitud pendiente.
                            </a>
                        @elseif ($pendingEvents->count()>1)
                            <a href="{{ route('event_spaces.awaitingRequests') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-700 dark:text-gray-700">
                                Hay {{ $pendingEvents->count() }} solicitudes pendientes.
                            </a>
                        @endif
                    </div>
                @endhasrole

                <!-- Imagen y enlace Mis eventos -->
                <a href="{{ route('events.my-events') }}">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 cursor-pointer border border-gray-700 dark:border-gray-400">
                        <img src="{{ asset('images/mis_eventos.png') }}" alt="Mis eventos" class="mx-auto h-20">
                        <p class="text-center mt-2 text-gray-900 dark:text-gray-100">Mis eventos</p>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>
