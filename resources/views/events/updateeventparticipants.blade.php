<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registro de participantes') }}
        </h2>
        <div class="text-gray-700 dark:text-gray-300">
            Selecciona a los(as) académicos que participarán en el evento, en caso de que no estén en la lista selecciona la opción "Otro(a) académico(a)."
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">

        {{-- Código para el manejo de notificaciones --}}
        @if(isset($message))
            <div class="{{ isset($academico) ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }} p-4 mb-4 rounded-md">
                {{ $message }}
            </div>
        @endif
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-2 gap-2 mx-auto sm:px-1 lg:px-2">
            <!-- Bloque para agregar participantes de la entidad -->
            <form action="{{ route('eventparticipant.updateparticipant') }}" method="POST" class="space-y-4">
                @csrf
                <div class="p-2 border border-gray-700 dark:border-gray-300">
                    <!-- Selección del académico -->
                    <div>
                        <label for="fullname" class="block font-bold mb-2">Nombre del académico(a)
                            <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Busca al académico(a) de la Facultad de Psicología escribiendo su nombre y apellidos o bien, selecciona 'Otro(a) académico(a)' en caso de que el académico(a) no esté en la lista.">?</span>
                        </label>
                        <select name="fullname" class="js-example-basic-single dark:bg-gray-800 dark:text-white" id="fullname" placeholder="Nombre del académico(a)" required>
                            <option value="">Selecciona un académico</option>
                            <option value="other_academic" {{ old('fullname') == 'other_academic' ? 'selected' : '' }}>Otro(a) académico(a)</option>
                            @foreach ($academics as $academic)
                                <option value="{{$academic->name}}" {{ old('fullname') == $academic->name ? 'selected' : '' }}>{{$academic->name}}</option>
                            @endforeach                        
                        </select>
                        @error('fullname')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror                
                    </div>

                    <!-- Campo Otro académico -->
                    <div class="mt-2" id="other-academic-container" style="{{ $errors->has('academic') ? 'display: block;' : 'display: none;' }}">
                        <label for="degree_academic" class="block font-bold mb-2">Grado académico
                            <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Seleciona el grado académico.">?</span>
                        </label>
                        <select name="degree_academic" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('degree_academic') border-red-500 @enderror" id="degree_academic" placeholder="Grado del académico(a)">
                            <option value="">Selecciona el grado académico</option>
                            <option value="C." {{ old('degree_academic') == 'C.' ? 'selected' : '' }}>C.</option>
                            <option value="Lic." {{ old('degree_academic') == 'Lic.' ? 'selected' : '' }}>Lic.</option>
                            <option value="Ing." {{ old('degree_academic') == 'Ing.' ? 'selected' : '' }}>Ing.</option>
                            <option value="Mtro." {{ old('degree_academic') == 'Mtro.' ? 'selected' : '' }}>Mtro.</option>
                            <option value="Mtra." {{ old('degree_academic') == 'Mtra.' ? 'selected' : '' }}>Mtra.</option>
                            <option value="Dr." {{ old('degree_academic') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                            <option value="Dra." {{ old('degree_academic') == 'Dra.' ? 'selected' : '' }}>Dra.</option>
                        </select>
                        @error('degree_academic')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror

                        <label for="other_academic_name" class="block font-bold mb-2">Nombre completo del académico(a) comenzando por nombre de pila y después apellidos.<span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el grado y nombre completo del académico comenzando por apellidos, por ejemplo Mtro. Eric Romero Martínez.">?</span></label>
                        <input type="text" name="other_academic_name" id="other_academic_name" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_academic_name') border-red-500 @enderror" value="{{ old('other_academic_name') }}">
                        @error('other_academic_name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-other-academic-name">250</span></p>

                        <label for="other_academic_email" class="block font-bold mb-2">Correo electrónico del académico(a) <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el correo electrónico del académico. Se generará una nueva contraseña y se le enviará por correo electrónico para dar seguimiento a la publicación de su evento.">?</span></label>
                        <input type="text" name="other_academic_email" id="other_academic_email" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_academic_email') border-red-500 @enderror" value="{{ old('other_academic_email') }}">
                        @error('other_academic_email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror

                        <label for="external_academic" class="block font-bold mt-2">
                            <input type="checkbox" name="external_academic" id="external_academic" value="1" {{ old('external_academic') ? 'checked' : '' }}>
                            El o la académico(a) es externo a la entidad <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="En caso de que el(la) académico(a) sea invitado(a) en la entidad, selecciona esta casilla.">?</span>
                        </label>
                        

                    </div>
                </div>        
                
                <div class="p-2 border border-gray-700 dark:border-gray-300">
                    <!-- Selección del tipo de participante -->
                    <div>
                        <label for="participation_type_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Tipo de Participación: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Selecciona el tipo de participación del académico(a) seleccionado o bien, selecciona 'Otro' para agregar otro tipo de participación que no esté en la lista.">?</span></label>
                        <select name="participation_type_id" id="participation_type_id" class="dark:bg-gray-800 dark:text-white" required>
                            <option value="">Seleccionar tipo de participación</option>
                            <option value="other" {{ old('participation_type_id') == 'other' ? 'selected' : '' }}>Otro</option>
                            @foreach($participationTypes as $participationType)
                                <option value="{{ $participationType->id }}" {{ old('participation_type_id') == $participationType->id ? 'selected' : '' }}>{{ $participationType->name }}</option>
                            @endforeach
                        </select>
                        @error('participation_type_id')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>
                    

                    <!-- Campo para agregar otro tipo de participación -->
                    <div class="mb-4" id="other-participation-container" style="{{ $errors->has('participation_type_id') ? 'display: block;' : 'display: none;' }}">
                        <label for="other_participation" class="block dark:text-gray-300 font-bold mb-2">Indica el tipo de participación: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="No debe exceder los 250 caracteres incluyendo espacios en blanco.">?</span></label>
                        <input type="text" name="other_participation" id="other" maxlength="250" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_participation') border-red-500 @enderror" value="{{ old('other_participation') }}">
                        @error('other_participation')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-other-participation">250</span></p>
                    </div>
                </div>
                <!-- Campo oculto para el ID del evento -->
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <!-- Botón para registrar al participante -->
                <div class="flex items-center">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">{{ __('Registrar participante') }}</button>
                </div>

            </form>
            
            <!-- Bloque para agregar participantes externos -->
            {{-- <div class="p-2 ml-2 border border-gray-700 dark:border-gray-300">
                <h2 class="text-lg border-b border-gray-700 dark:border-gray-500">Registro de académico(a) externo.</h2>
                <form action="{{ route('eventparticipant.storeparticipant') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Campos para llenar en caso de que el usuario no exista en la base de datos -->    
                    <label for="fullname" class="block font-bold mb-2">Grado y nombre del académico(a)
                        <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Registra al académico(a) escribiendo su grado, nombre y apellidos.">?</span>
                    </label>
                    <input class="dark:bg-gray-800 dark:text-white" type="text" name="fullname" id="fullname" required/>

                    <!-- Selección del tipo de participante -->
                    <label for="participation_type_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Tipo de Participación:</label>
                    <select name="participation_type_id" id="participation_type_id" class="dark:bg-gray-800 dark:text-white">
                        <option value="">Seleccionar tipo de participación</option>
                        @foreach($participationTypes as $participationType)
                            <option value="{{ $participationType->id }}">{{ $participationType->name }}</option>
                        @endforeach
                        <option value="other">Otro</option>
                    </select>

                    <!-- Campo para agregar otro tipo de participación -->
                    <div class="mb-4" id="other-container" style="display: none;">
                        <label for="other" class="block dark:text-gray-300 font-bold mb-2">Indica el tipo de participación: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="No debe exceder los 250 caracteres incluyendo espacios en blanco.">?</span></label>
                        <input type="text" name="other" id="other" maxlength="250" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other') border-red-500 @enderror" value="{{ old('other') }}" required>
                        
                        @error('other')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campo oculto para el ID del evento -->
                    <input type="hidden" name="event_id" value="{{ $event->id }}">

                    <!-- Botón para registrar al participante -->
                    <div class="flex items-center">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">{{ __('Registrar participante externo') }}</button>
                    </div>

                </form>
            </div> --}}
        </div>

        <div class="p-6">
            <h2 class="text-lg font-semibold mb-2">Lista de Participantes</h2>
            <!-- Lista de participantes -->
            @if ($participants!=null&&$participants->count() > 0)
                <table class="border border-gray-700 dark:border-gray-300">
                    <thead class="bg-gray-300 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2">Nombre completo</th>
                            <th class="px-4 py-2">Tipo de participación</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($participants as $participant)
                            <tr>
                                <td class="px-4 py-2">
                                    @if($participant->user_id!=null)
                                        {{ $participant->user->degree }}
                                    @endif 
                                    {{ $participant->fullname }}
                                </td>
                                <td class="px-4 py-2">{{ $participant->participationType->name }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('eventparticipant.delete.update', ['participant' => $participant->id]) }}"
                                    class="text-red-500"
                                    onclick="return confirm('¿Estás seguro de que deseas quitar a este participante?')">
                                        Quitar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No hay participantes agregados al evento.</p>
            @endif
        </div>

        <!-- En caso de que aún no haya iniciado el registro, se muestra el botón -->
        
        <div class="flex mt-4 mb-4">
            <!-- Botón para salir de la actualización -->
            <a href="{{route('dashboard')}}" class="mx-2 px-4 py-2 bg-orange-500 text-white font-semibold rounded-md">
                {{ $event->status=='borrador'?__('Guardar borrador'):__('Regresar'); }}
            </a>

            @if ($event->start_date > now() && $event->status=='borrador')
                <!-- Botón para cerrar el registro -->
                <a href="{{route('events.register',$event->id)}}" class="mx-2 px-4 py-2 bg-green-500 text-white font-semibold rounded-md"
                    onclick="return confirm('Una vez registrado el evento no podrá modificar la información primordial como lo es el título, fechas y espacio solicitado del evento. ¿Desea continuar?')">
                    {{ __('Registrar evento') }}
                </a>
                <!-- Botón para cancelar registro -->
                <form action="{{ route('event.destroy', $event->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <button type="submit" class="mx-2 px-4 py-2 bg-red-500 text-white font-semibold rounded-md" onclick="return confirm('¿Estás seguro de que deseas cancelar este registro?')">
                        {{ __('Cancelar registro') }}
                    </button>
                </form>    
            @endif
            
            
        </div>

    </div>

</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>

<script>
    tippy('[data-tippy-content]');
</script>

<script>
    $(document).ready(function () {
        // Manejador de eventos para la entrada en el campo de entrada adicional
        $("#other_academic_name").on('input', function () {
            // Actualiza el conteo de caracteres restantes
            const charCount = 250 - $(this).val().length;
            $("#char-count-other-academic-name").text(charCount);
        });

        // Manejador de eventos para el cambio en la selección de nombre del académico
        $("#fullname").change(function () {
            // Oculta el campo de entrada adicional si la opción seleccionada no es "other_academic"
            if ($(this).val() !== "other_academic") {
                $("#other-academic-container").hide();
            } else {
                // Muestra el campo de entrada adicional si la opción seleccionada es "other_academic"
                $("#other-academic-container").show();
            }
        });

        // Agregamos un evento adicional para manejar la visibilidad inicial basada en el valor seleccionado
        $("#fullname").trigger('change');

        // Manejador de eventos para la entrada en el campo de entrada adicional (otro tipo de participación)
        $("#other").on('input', function () {
            // Actualiza el conteo de caracteres restantes
            const charCount = 250 - $(this).val().length;
            $("#char-count-other-participation").text(charCount);
        });

        // Manejador de eventos para el cambio en la selección de Tipo de participación
        $("#participation_type_id").change(function () {
            // Oculta el campo de entrada adicional si la opción seleccionada no es "other"
            if ($(this).val() !== "other") {
                $("#other-participation-container").hide();
            } else {
                // Muestra el campo de entrada adicional si la opción seleccionada es "other"
                $("#other-participation-container").show();
            }
        });

        // Agregamos un evento adicional para manejar la visibilidad inicial basada en el valor seleccionado
        $("#participation_type_id").trigger('change');
    });
</script>

