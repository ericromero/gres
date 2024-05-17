<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Crear evento rápido') }}
        </h2>

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

        <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
            <p>Ingresa la información mínima de tu evento privado o interno.</p>        
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">                
                <div class="p-2">
                    <form action="{{ route('events.store.private') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div>
                            @if (isset($space)&&$space->name!=null)
                                <h2 class="block font-bold mb-2">Espacio solicitado: <span class="text-gray-500 dark:text-gray-300">{{$space->name}}</span></h2>
                                <input name="space" type="hidden" value="{{$space->id}}">
                            @else
                                <h2 class="block font-bold mb-2">Espacio solicitado: <span class="text-gray-500 dark:text-gray-300">Evento virtual</span>.</h2>
                            @endif                            
                        </div>

                        <div class="mb-4 block font-bold">
                            @if ($start_date_string==$end_date_string)
                                Fecha: {{$start_date_string}} de {{$start_time}} a {{$end_time}} horas.
                            @else
                                Periodo: del {{$start_date_string}} al {{$end_date_string}}, de {{$start_time}} a {{$end_time}} horas.
                            @endif                            
                        </div>

                        <!-- Departamento solicitante -->
                        <div class="mb-4">
                            <label for="department" class="block font-bold mb-2">Departamento solicitante: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Selecciona el departamento del cual solicitas el evento.">?</span></label>
                            <select name="department" id="department" class="js-example-basic-single form-select dark:bg-gray-800 dark:text-white @error('department') border-red-500 @enderror" required>
                                <option value="">Selecciona el departamento solicitante</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>                            
                            @error('department')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de evento -->
                        <input type="hidden" name="event_type_id" value="1">
                        {{-- <div class="mb-4">
                            <label for="event_type_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Tipo de Evento: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="En caso de no encontrar el tipo de Evento adecuado, selecciona la opción Otro e ingresa la información correspondiente.">?</span></label>
                            <select name="event_type_id" id="event_type_id" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('event_type_id') border-red-500 @enderror" required>
                                <option value="">Seleccionar tipo de evento</option>
                                @foreach($eventTypes as $eventType)
                                    <option value="{{ $eventType->id }}" {{ old('event_type_id') == $eventType->id ? 'selected' : '' }}>{{ $eventType->name }}</option>
                                @endforeach
                                <option value="Other">Otro</option>
                            </select>
                            @error('event_type_id')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        
                        <!-- Otro espacio solicitado  -->
                        {{-- <div class="mb-4" id="other-container">
                            <label for="other" class="block dark:text-gray-300 font-bold mb-2">Indica que tipo de evento: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="No debe exceder los 250 caracteres incluyendo espacios en blanco.">?</span></label>
                            <input type="text" name="other" id="other" maxlength="250" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other') border-red-500 @enderror" value="{{ old('other') }}">
                            
                            @error('other')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-other">250</span></p>

                            <label for="category" class="block font-bold mb-2">Categoria: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="Selecciona la categoria a la que corresponde el nuevo evento">?</span>
                            </label>
                            <select name="category" id="category" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('category') border-red-500 @enderror">
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <!-- Título -->
                        <div class="mb-4">
                            <label for="title" class="block dark:text-gray-300 font-bold mb-2">Título: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="No debe exceder los 250 caracteres incluyendo espacios en blanco.">?</span></label>                            
                            <input type="text" name="title" id="title" maxlength="250" class="w-full form-input dark:bg-gray-800 dark:text-white @error('title') border-red-500 @enderror" value="{{ old('title') }}" required>
                            
                            @error('title')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count">250</span></p>
                        </div>

                        <!-- Responsable -->
                        <div class="border p-2 border-gray-700 dark:border-gray-300">                            
                            <div class="mb-4">
                                <label for="responsible" class="block font-bold mb-2">Solicitante (responsable de organizar el evento): <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                    data-tippy-content="Selecciona al académico(a) que está organizando el evento y es responsable de realizar los trámites necesarios para llevarlo a cabo. En caso de no encontarlo en la lista, selecciona 'Otro responsable' e ingresa la información.">?</span>
                                </label>
                                <select name="responsible" id="responsible" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('responsible') border-red-500 @enderror" required>
                                    <option value="">Seleccionar responsable</option>
                                    <option value="other_responsible" {{ old('responsible') == 'other_responsible' ? 'selected' : '' }}>Otro(a) responsable</option>
                                    @foreach($academicos as $academico)
                                        <option value="{{ $academico->id }}" {{ old('responsible') == $academico->id ? 'selected' : '' }}>{{ $academico->name }}</option>
                                    @endforeach
                                </select>
                                @error('responsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Otro responsable -->
                            <div class="mt-2" id="other-responsible-container" style="{{ $errors->has('responsible') ? 'display: block;' : 'display: none;' }}">
                                <label for="degree_responsible" class="block font-bold mb-2">Grado académico
                                    <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Seleciona el grado académico.">?</span>
                                </label>
                                <select name="degree_responsible" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('degree_responsible') border-red-500 @enderror" id="degree_responsible" placeholder="Grado del académico(a)">
                                    <option value="">Selecciona el grado académico</option>
                                    <option value="C." {{ old('degree_responsible') == 'C.' ? 'selected' : '' }}>C.</option>
                                    <option value="Lic." {{ old('degree_responsible') == 'Lic.' ? 'selected' : '' }}>Lic.</option>
                                    <option value="Ing." {{ old('degree_responsible') == 'Ing.' ? 'selected' : '' }}>Ing.</option>
                                    <option value="Mtro." {{ old('degree_responsible') == 'Mtro.' ? 'selected' : '' }}>Mtro.</option>
                                    <option value="Mtra." {{ old('degree_responsible') == 'Mtra.' ? 'selected' : '' }}>Mtra.</option>
                                    <option value="Dr." {{ old('degree_responsible') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                    <option value="Dra." {{ old('degree_responsible') == 'Dra.' ? 'selected' : '' }}>Dra.</option>
                                </select>
                                
                                @error('degree_responsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                                <label for="other_responsible_name" class="block font-bold mb-2">Nombre completo del(la) responsable comenzando por nombre y después apellidos.<span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el grado y nombre completo del académico comenzando por apellidos, por ejemplo Mtro. Eric Romero Martínez.">?</span></label>
                                <input type="text" name="other_responsible_name" id="other_responsible_name" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_responsible_name') border-red-500 @enderror" value="{{ old('other_responsible_name') }}">
                                @error('other_responsible_name')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-other-resposible-name">250</span></p>

                                <label for="email_responsible" class="block font-bold mb-2">Correo electrónico del académico(a) <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el correo electrónico del académico. Se generará una nueva contraseña y se le enviará por correo electrónico para dar seguimiento a la publicación de su evento.">?</span></label>
                                <input type="text" name="email_responsible" id="email_responsible" class="w-full form-input dark:bg-gray-800 dark:text-white @error('email_responsible') border-red-500 @enderror" value="{{ old('email_responsible') }}">
                                @error('email_responsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                            </div>
                        </div>


                        <!-- Fechas y horarios -->
                        <div class="flex">
                            <div>
                                <input type="hidden" name="start_date" id="start_date" class="form-input @error('start_date') border-red-500 @enderror" value="{{ $start_date}}" min="{{ now()->addDays(4)->format('Y-m-d') }}" max="{{ now()->addMonths(6)->format('Y-m-d') }}" required>
                                @error('start_date')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="ml-4">
                                <input type="hidden" name="end_date" id="end_date" class="form-input @error('end_date') border-red-500 @enderror" value="{{$end_date}}" min="{{ now()->addDays(4)->format('Y-m-d') }}" max="{{ now()->addMonths(6)->format('Y-m-d') }}" required>
                                    @error('end_date')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <input type="hidden" name="start_time" id="start_time" class="form-input @error('start_time') border-red-500 @enderror" value="{{$start_time}}" required readonly>
                                @error('start_time')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="ml-4">
                                <input type="hidden" name="end_time" id="end_time" class="form-input @error('end_time') border-red-500 @enderror" value="{{$end_time}}" required readonly>
                                @error('end_time')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Audiencia -->
                        <input type="hidden" name="audience" value="1">
                        {{-- <div class="mb-4">
                            <label for="audience" class="block font-bold mb-2">Audiencia: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="Selecciona a quién va dirigido el evento.">?</span>
                            </label>
                            <select name="audience" id="audience" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('audience') border-red-500 @enderror" required>
                                <option value="">Selecciona la audiencia</option>
                                @foreach($audiences as $audience)
                                    <option value="{{ $audience->id }}" {{ old('audience') == $audience->id ? 'selected' : '' }}>{{ $audience->name }}</option>
                                @endforeach
                            </select>
                            @error('audience')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <!-- Area de conocimiento -->
                        <input type="hidden" name="knowledge_area" value="1">
                        {{-- <div class="mb-4">
                            <label for="knowledge_area" class="block font-bold mb-2">Campo de conocimiento: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="Selecciona el campo de conocimiento">?</span>
                            </label>
                            <select name="knowledge_area" id="knowledge_area" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('knowledge_area') border-red-500 @enderror" required>
                                <option value="">Selecciona el campo de conocimiento</option>
                                @foreach($knowledge_areas as $knowledge_area)
                                    <option value="{{ $knowledge_area->id }}" {{ old('knowledge_area') == $knowledge_area->id ? 'selected' : '' }}>{{ $knowledge_area->name }}</option>
                                @endforeach
                            </select>
                            @error('knowledge_area')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <div class="form-check">
                            <h3 class="font-bold">Lineamientos en el uso de espacios</h3>
                            <input class="form-check-input {{ $errors->has('agreeTerms') ? 'is-invalid' : '' }}" type="checkbox" id="agreeTerms" name="agreeTerms">
                            <label class="form-check-label" for="agreeTerms">
                                He leído y estoy de acuerdo con los <a href="{{ route('terms') }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline dark:texte-blue-200 dark:hover:text-blue-400">lineamientos del espacio solicitado</a>. 
                            </label>
                            <p id="termsError" style="display:none; color:red;">
                                Tiene que estar de acuerdo con los lineamientos del espacio solicitado.
                            </p>
                            @error('agreeTerms')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror  
                        </div>

                        <!-- Nota sobre la solicitud de recursos de UDEMAT -->
                        <div class="border p-4 mt-2 border-gray-800 dark:border-gray-300">
                            <b>Nota:</b> Si requiere servicio de grabación, fotografia o transmisión, por favor acuda directamente a UDEMAT para solicitar el servicio.
                        </div>
                        
                        <div class="flex">
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" id="submitButton" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Registrar Evento</button>
                            </div>

                            <div class="flex items-center justify-end mt-4 ml-4">
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Cancelar registro</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>

<script>
    const requiresRegistrationCheckbox = document.getElementById('registration_required');
    const registrationUrlContainer = document.getElementById('registration_url_container');
    const registrationUrlInput = document.getElementById('registration_url');

    requiresRegistrationCheckbox.addEventListener('change', () => {
        if (requiresRegistrationCheckbox.checked) {
            registrationUrlContainer.classList.remove('hidden');
            registrationUrlInput.setAttribute('required', true);
        } else {
            registrationUrlContainer.classList.add('hidden');
            registrationUrlInput.removeAttribute('required');
        }
    });
</script>

<script>
    tippy('[data-tippy-content]');
</script>

<script>
    const titleInput = document.getElementById('title');
    const charCount = document.getElementById('char-count');

    titleInput.addEventListener('input', function() {
        charCount.textContent = 250 - titleInput.value.length;
    });
</script>

<script>
    const otherInput = document.getElementById('other');
    const charCount = document.getElementById('char-count-other');

    otherInput.addEventListener('input', function() {
        charCount.textContent = 250 - otherInput.value.length;
    });
</script>

<!-- Contador de caracteres del resumen -->
<script>
    const textarea_summary = document.getElementById('summary');
    const counter_summary = document.getElementById('char-count-summary');

    textarea_summary.addEventListener('input', function () {
        const maxLength = parseInt(textarea_summary.getAttribute('maxlength'), 10);
        const currentLength = textarea_summary.value.length;

        if (currentLength > maxLength) {
            textarea_summary.value = textarea_summary.value.substring(0, maxLength);
        }

        counter_summary.textContent = `${currentLength}/${maxLength}`;
    });
</script>

<!-- Contador de caracteres de requisitos adicionales -->
<script>
    const textarea_requirements = document.getElementById('requirements');
    const counter_requirements = document.getElementById('char-count-requirements');
    textarea_requirements.addEventListener('input', function () {
        const maxLength = parseInt(textarea_requirements.getAttribute('maxlength'), 10);
        const currentLength = textarea_requirements.value.length;

        if (currentLength > maxLength) {
            textarea_requirements.value = textarea_requirements.value.substring(0, maxLength);
        }

        counter_requirements.textContent = `${currentLength}/${maxLength}`;
    });
</script>

<script>
    const fileInput = document.getElementById('cover_image');
    const maxSize = 5242880; // Tamaño máximo en bytes (5 MB)

    fileInput.addEventListener('change', function () {
        const allowedFormats = ['image/jpeg', 'image/png', 'image/jpg'];
        const selectedFile = this.files[0];

        if (!selectedFile) {
            return; // No se seleccionó ningún archivo
        }

        const selectedFileType = selectedFile.type;
        const selectedFileSize = selectedFile.size;

        if (!allowedFormats.includes(selectedFileType)) {
            alert('Por favor, seleccione un archivo de tipo .jpg, .jpeg o .png.');
            this.value = ''; // Limpia el valor del input para permitir una nueva selección
            return;
        }

        if (selectedFileSize > maxSize) {
            alert('El archivo seleccionado es demasiado grande. El tamaño máximo permitido es de 5 MB.');
            this.value = ''; // Limpia el valor del input para permitir una nueva selección
        }
    });
</script>

<script>
    const fileInput = document.getElementById('program');
    const maxSize = 5242880; // Tamaño máximo en bytes (5 MB)

    fileInput.addEventListener('change', function () {
        const allowedFormat = 'application/pdf';
        const selectedFile = this.files[0];

        if (!selectedFile) {
            return; // No se seleccionó ningún archivo
        }

        const selectedFileType = selectedFile.type;
        const selectedFileSize = selectedFile.size;

        if (selectedFileType !== allowedFormat) {
            alert('Por favor, seleccione un archivo en formato PDF.');
            this.value = ''; // Limpia el valor del input para permitir una nueva selección
            return;
        }

        if (selectedFileSize > maxSize) {
            alert('El archivo seleccionado es demasiado grande. El tamaño máximo permitido es de 5 MB.');
            this.value = ''; // Limpia el valor del input para permitir una nueva selección
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Ocultar el campo other al inicio
        $('#other-container').hide();

        // Manejar el cambio en la lista desplegable
        $('#event_type_id').change(function() {
            // Mostrar u ocultar el campo other según la selección
            if ($(this).val() === 'Other') {
                $('#other-container').show();
            } else {
                $('#other-container').hide();
            }
        });
    });

    // Delegar el evento 'input' al documento para manejar campos que aparecen después de cargar la página
    $(document).on('input', '#other', function() {
            // Actualizar el conteo de caracteres
            $('#char-count-other').text(250 - $(this).val().length);
        });
</script>

<!-- Mostrar/Ocultar campos para agregar responable y corresponsable -->
<script>
    $(document).ready(function () {
        // Manejador de eventos para la entrada en el campo de entrada adicional (otros corresponsables)
        $("#other_coresponsible_name").on('input', function () {
            // Actualiza el conteo de caracteres restantes
            const charCount = 250 - $(this).val().length;
            $("#char-count-other-coresponsible-name").text(charCount);
        });

        // Manejador de eventos para el cambio en la selección de corresponsable
        $("#coresponsible").change(function () {
            // Oculta el campo de entrada adicional si la opción seleccionada no es "other_coresponsible"
            if ($(this).val() !== "other_coresponsible") {
                $("#other-coresponsible-container").hide();
            } else {
                // Muestra el campo de entrada adicional si la opción seleccionada es "other_coresponsible"
                $("#other-coresponsible-container").show();
            }
        });

        // Agregamos un evento adicional para manejar la visibilidad inicial basada en el valor seleccionado
        $("#coresponsible").trigger('change');

        // Manejador de eventos para la entrada en el campo de entrada adicional (otros responsables)
        $("#other_responsible_name").on('input', function () {
            // Actualiza el conteo de caracteres restantes
            const charCount = 250 - $(this).val().length;
            $("#char-count-other-responsible-name").text(charCount);
        });

        // Manejador de eventos para el cambio en la selección de responsable
        $("#responsible").change(function () {
            // Oculta el campo de entrada adicional si la opción seleccionada no es "other_responsible"
            if ($(this).val() !== "other_responsible") {
                $("#other-responsible-container").hide();
            } else {
                // Muestra el campo de entrada adicional si la opción seleccionada es "other_responsible"
                $("#other-responsible-container").show();
            }
        });

        // Agregamos un evento adicional para manejar la visibilidad inicial basada en el valor seleccionado
        $("#responsible").trigger('change');
    });

</script>

<script>
    $(document).ready(function () {
        // Manejador de eventos para el envío del formulario
        $('form').submit(function (event) {
            // Obtén los valores de los correos electrónicos del responsable y corresponsable
            var emailResponsible = $('#email_responsible').val();
            var emailCoresponsible = $('#email_coresponsible').val();

            // Verifica si los correos electrónicos son iguales
            if (emailResponsible === emailCoresponsible&&emailResponsible!="") {
                // Evita que se envíe el formulario
                alert('El correo del responsable y corresponsable deben ser diferentes.');
                event.preventDefault();
            }
        });
    });
</script>

<script>
    document.getElementById('submitButton').addEventListener('click', function(event) {
        var checkbox = document.getElementById('agreeTerms');
        var errorMessage = document.getElementById('termsError');
        
        if (!checkbox.checked) {
            errorMessage.style.display = 'block'; // Mostrar el mensaje de error
            event.preventDefault(); // Prevenir que el formulario se envíe
        } else {
            errorMessage.style.display = 'none'; // Ocultar el mensaje de error
        }
    });
    </script>





