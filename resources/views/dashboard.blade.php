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
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">                        
                        <div>
                            <a href="{{ route('users.index') }}">
                                <img src="{{ asset('images/usuarios.png') }}" alt="Usuarios" class="mx-auto h-20">
                            </a>                            
                        </div>

                        <div>
                            <a href="{{ route('users.index') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Buscar usuario</a>
                        </div>                        
                    </div>
                @endhasrole

                <!-- Imagen y enlace para gestionar departamentos -->
                @hasrole('Administrador')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('departments.index') }}">
                                <img src="{{ asset('images/departamento.png') }}" alt="Departamentos" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('departments.index') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Ver departamentos </a>
                        </div>
                    </div>
                @endhasrole

                <!-- Imagen y enlace para gestionar espacios -->
                @hasrole('Administrador')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('spaces.index') }}">
                                <img src="{{ asset('images/espacios.png') }}" alt="Espacios" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('spaces.index') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Ver espacios</a>
                        </div>
                    </div>
                @endhasrole

                <!-- Imagen y enlace para consultar espacios -->
                @hasanyrole('Coordinador|Gestor de espacios')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('spaces.my-spaces') }}">
                                <img src="{{ asset('images/espacios.png') }}" alt="Mis espacios y recursos" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('spaces.my-spaces') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Mis espacios y recursos</a>
                        </div>
                    </div>
                @endhasrole

                <!-- Imagen y enlace para contruir el equipo de trabajo -->
                @hasanyrole('Administrador|Coordinador')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('users.team') }}">
                                <img src="{{ asset('images/equipo.png') }}" alt="Equipo de trabajo" class="mx-auto h-20">
                            </a>
                        </div>
                        
                        <div>
                            <a href="{{ route('users.team') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Equipo de trabajo</a>
                        </div>

                        @hasanyrole('Administrador')
                            <div>
                                <a href="{{ route('users.team') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Gestionar equipos de trabajo</a>
                            </div>
                        @endhasrole

                    </div>
                @endhasrole

                <!-- Imagen y enlace para revisar la agenda de día -->
                @hasanyrole('Coordinador|Gestor de eventos')
                <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                    <div>
                        <a href="{{ route('events.byDay') }}">
                            <img src="{{ asset('images/evento.png') }}" alt="Solicitar espacio" class="mx-auto h-20">
                        </a>
                    </div>

                    <div>
                        <a href="{{ route('events.byDay') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Eventos del día</a>
                    </div>

                    <!-- Notificación de eventos por atender al día -->
                    <div>
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
                </div>
                @endhasrole

                <!-- Imagen y enlace para crear un nuevo evento -->
                @hasanyrole('Coordinador|Gestor de eventos')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('spaces.search') }}">
                                <img src="{{ asset('images/calendario.png') }}" alt="Solicitar espacio" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('spaces.search') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Registrar evento</a>
                        </div>
                    </div>
                @endhasrole

                <!-- Imagen y enlace para revisar los eventos solicitados por la coordinación -->
                @hasanyrole('Coordinador|Gestor de eventos')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('events.byArea') }}">
                                <img src="{{ asset('images/autorizacioneventos.png') }}" alt="Autorización de eventos" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('events.byArea') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Eventos de la coordinación</a>
                        </div>
                        
                        <!-- Notificación de eventos en borrador -->
                        <div>
                            @if ($draftEvents->count()==1)
                                <a href="{{ route('events.byArea.drafts') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay un evento sin registrar.
                                </a>
                            @elseif ($draftEvents->count()>1)
                                <a href="{{ route('events.byArea.drafts') }}" class="block text-center rounded-lg shadow-lg p-1 m-2 border border-orange-600 bg-orange-300 hover:bg-orange-100 hover:text-gray-700 dark:bg-orange-200 text-gray-900 dark:text-gray-700">
                                    Hay {{ $draftEvents->count() }} eventos sin registrar.
                                </a>
                            @endif
                        </div>
                        
                        <!-- Notificación de eventos aceptado y no publicados -->  
                        <div>
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
                    </div>
                @endhasrole
                
                <!-- Imagen y enlace a los espacios solicitados por la coordinación -->
                @hasrole('Gestor de espacios')
                    <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                        <div>
                            <a href="{{ route('event_spaces.review') }}">
                                <img src="{{ asset('images/espacios_solicitados.png') }}" alt="Mis eventos" class="mx-auto h-20">
                            </a>
                        </div>

                        <div>
                            <a href="{{ route('event_spaces.review') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Espacios solicitados</a>
                        </div>

                        <!-- Notificación de espacios solicitados sin atender -->
                        <div>
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
                    </div>
                @endhasrole

                <!-- Imagen y enlace Mis eventos -->
                <div class="p-4 text-center bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-700 dark:border-gray-400">
                
                    <div>
                        <a href="{{ route('events.my-events') }}">
                            <img src="{{ asset('images/mis_eventos.png') }}" alt="Mis eventos" class="mx-auto h-20">
                        </a>
                    </div>

                    <div>
                        <a href="{{ route('events.my-events') }}" class="mt-2 text-blue-700 hover:text-blue-900 hover:underline dark:text-blue-100 dark:text-blue-300">Mis eventos</a>
                    </div>
                
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

    