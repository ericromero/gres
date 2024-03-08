<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cancelar evento') }}
        </h2>
        <div class="text-gray-600 mb-2">
            <p class="text-gray-900 dark:text-gray-100">
                Para cancelar este evento, escriba una justificación de entre 100 y 2000 caracteres (incluyendo espacios) y haga clic en Cancelar evento.
            </p>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 max-w-7xl mx-auto sm:px-6 lg:px-8 overflow-hidden shadow-sm sm:rounded-lg grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Contenedor con la información del curso a cancelar -->
        <div class="border border-gray-700 dark:border-gray-100 bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4">
            <!-- Título de evento -->
            <div class="ml-d text-center">                            
                <h2 class="text-xl font-semibold mb-2">{{ $event->title }}</h2>
            </div>

            <!-- Imagen del evento en minuatura -->
            <div class="ml-4">
                @if ($event->cover_image == null)                            
                    <img src="{{ asset('images/unam.png') }}" alt="{{ $event->title }}" class="w-20 h-20 object-cover dark:bg-slate-300">
                @else
                    <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" class="w-20 h-20 object-cover dark:bg-slate-300 cursor-pointer" onclick="window.open('{{ asset($event->cover_image) }}')">
                @endif
            </div>

            <!-- Sección de información-->
            <div class="p-4">
                <p><strong>Departamento solicitante:</strong>  {{$event->department->name}}</p>
                <p><strong>Persona responable del event:</strong> {{ $event->responsible->name }} <a href="mailto:{{ $event->responsible->email }}" class="text-blue-800 dark:text-blue-200"> {{ $event->responsible->email }}</a></p>
                <p><strong>Fecha:</strong> Del {{ $event->start_date }} al {{ $event->end_date }}</p>
                <p><strong>Horario:</strong> De {{ $event->start_time }} a {{ $event->end_time }}</p>
                
                @if ($event->registration_url!=null)
                    <p><strong>Registro:</strong> {{ $event->registration_url }}</p>
                @else
                    <p><strong>Registro:</strong> No se requiere</p>
                @endif

                <p><strong>Espacio solicitado:</strong>
                @if ($event->space_required)
                    @foreach($event->spaces as $eventspace)
                        {{$eventspace->name}} ({{$eventspace->location}})
                    @endforeach
                @else
                    No se requiere espacio
                @endif
                </p>
                
                @if($event->cancelled==1)
                    <div>
                        <strong>Motivo de cancelación:</strong> {{ $event->canceledEvent->cancellation_reason }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Formulario para cancelación del evento -->
        <div class="border border-gray-700 dark:border-gray-100 bg-white dark:bg-gray-800 overflow-hidden rounded-lg shadow-sm sm:rounded-lg mb-4">
            <form action="{{ route('event.cancel',$event) }}" method="POST">
                @csrf
                <div class="p-4">
                    <label class="block font-bold mb-2" for="justify">Motivo de cancelación</label><br>
                    <textarea name="justify" id="justify" cols="30" rows="10" maxlength="2000" class="w-full form-textarea dark:bg-gray-800 dark:text-white @error('justify') border-red-500 @enderror" required>{{ old('justify') }}</textarea>
                    @error('justify')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-justify">2000</span></p>
                </div>

                <div class="items-center justify-end ml-4">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Cancelar evento</button>
                </div>
            </form>
        </div>
    </div>

    <div class="ml-10 my-4">
        <a href="{{ url()->previous() }}" class="px-4 py-2 bg-orange-500 text-white font-semibold rounded-md">Regresar</a>
    </div>

</x-app-layout>

<script>
    const textarea = document.getElementById('justify');
    const counter = document.getElementById('char-count-justify');

    textarea.addEventListener('input', function () {
        const maxLength = parseInt(textarea.getAttribute('maxlength'), 10);
        const currentLength = textarea.value.length;

        if (currentLength > maxLength) {
            textarea.value = textarea.value.substring(0, maxLength);
        }

        counter.textContent = `${currentLength}/${maxLength}`;
    });
</script>