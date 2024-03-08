<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Selección de recursos para utilizar') }}
        </h2>
        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            <p>Por favor considere una adecuada selección de los recursos que requiere para su evento, ya que está selección <span class="text-red-900 dark:text-red-300">no podrá cambiarse una vez que el evento es autorizado</span>.</p>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">        
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

        @foreach ($event->spaces as $space)
            <p class="ml-2"><strong>Espacio:</strong> {{ $space->name }}</p>
            <div class="grid grid-cols-1 md:grid-cols-2">
                 <!-- Recursos reservados -->
                <div class="p-2 m-2 border border-gray-700 dark:border-gray-200">
                    <h2 class="font-semibold text-lg">Recursos solicitados</h2>
                    <p class="text-sm">Para quitar un recurso en la solicitud de este espacio, da clic en el símbolo <span class="text-red-700 dark:text-red-200"> rojo >></span> correspondiente.</p>
                    <hr class="my-2 border-gray-800 dark:border-gray-200">
                    <ul>
                        @foreach($reservedResources as $reservedResource)
                            <li>
                                {{ $reservedResource->resource->name }} 
                                <a href="{{ route('event.removeResource', ['event'=>$event->id,'reservedResource' => $reservedResource->id]) }}" class="text-red-700 dark:text-red-200">>></a>
                            </li>
                        @endforeach
                    </ul>                    
                </div>
                    
                <!-- Recursos no reservados -->                
                <div class="p-2 m-2 border border-gray-700 dark:border-gray-200">
                    <h2 class="font-semibold text-lg">Recursos disponibles</h2>                    
                    <p class="text-sm">Para solicitar un recurso de este espacio, da clic en el símbolo <span class="text-green-700 dark:text-green-200"> verde <<</span> correspondiente.</p>
                    <hr class="my-2 border-gray-800 dark:border-gray-200">
                    <ul>
                        @foreach($availableResources as $availableResource)                        
                            <li>
                                <a href="{{ route('event.addResource',['event' => $event->id, 'resource' => $availableResource->id]) }}" class="text-green-700 dark:text-green-200"><<</a> {{ $availableResource->name }}
                            </li>
                        @endforeach
                    </ul>
                    
                </div>    
            </div>
        @endforeach
        
        {{-- Botones de acción --}}
        <div class="flex">
            <div class="p-2 mt-4 bg-blue-500 text-white rounded">
                <a href="{{ route('events.participants',$event) }}" >Continuar con la selección de participantes</a>
            </div>
            <div class="p-2 ml-2 mt-4 bg-orange-500 text-white rounded">
                <a href="{{ route('dashboard') }}">Regresar a la pantalla principal</a>
            </div>
        </div>
            
           

    </div>
</x-app-layout>
