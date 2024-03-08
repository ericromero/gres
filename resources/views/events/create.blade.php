<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Crear Evento') }}
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
            <p>Ingresa toda la información de tu evento, en caso de que se requiera realizar el registro, escribe la URL de la herramienta de registro.</p>        
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">                
                <div class="p-2">
                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
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
                            Fecha o periodo: del {{$start_date}} al {{$end_date}}, de {{$start_time}} a {{$end_time}} horas.
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
                        <div class="mb-4">
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
                        </div>
                        
                        <!-- Otro espacio solicitado  -->
                        <div class="mb-4" id="other-container">
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
                        </div>

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

                        <!-- Corresponsable -->
                        <div class="my-2 p-2 border border-gray-700 dark:border-gray-300">                            
                            <div class="mb-4">
                                <label for="coresponsible" class="block font-bold mb-2">Corresponsable: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="En caso de que el (la) responsable no pueda continuar con la organización del evento, el(la) corresponsable dará continuidad a la organización del evento.">?</span></label>
                                <select name="coresponsible" id="coresponsible" class="js-example-basic-single form-select dark:bg-gray-800 dark:text-white @error('coresponsible') border-red-500 @enderror" required>
                                    <option value="">Seleccionar corresponsable</option>
                                    <option value="other_coresponsible" {{ old('coresponsible') == 'other_coresponsible' ? 'selected' : '' }}>Otro(a) corresponsable</option>
                                    @foreach($academicos as $academico)
                                        <option value="{{ $academico->id }}" {{ old('coresponsible') == $academico->id ? 'selected' : '' }}>{{ $academico->name }}</option>
                                    @endforeach
                                </select>
                                @error('coresponsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Otro coresponsable -->
                            <div class="mt-2" id="other-coresponsible-container" style="{{ $errors->has('coresponsible') ? 'display: block;' : 'display: none;' }}">
                                <label for="degree_coresponsible" class="block font-bold mb-2">Grado académico
                                    <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Seleciona el grado académico.">?</span>
                                </label>
                                <select name="degree_coresponsible" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('degree_coresponsible') border-red-500 @enderror" id="degree_coresponsible" placeholder="Grado del académico(a)">
                                    <option value="">Selecciona el grado académico</option>
                                    <option value="C." {{ old('degree_coresponsible') == 'C.' ? 'selected' : '' }}>C.</option>
                                    <option value="Lic." {{ old('degree_coresponsible') == 'Lic.' ? 'selected' : '' }}>Lic.</option>
                                    <option value="Ing." {{ old('degree_coresponsible') == 'Ing.' ? 'selected' : '' }}>Ing.</option>
                                    <option value="Mtro." {{ old('degree_coresponsible') == 'Mtro.' ? 'selected' : '' }}>Mtro.</option>
                                    <option value="Mtra." {{ old('degree_coresponsible') == 'Mtra.' ? 'selected' : '' }}>Mtra.</option>
                                    <option value="Dr." {{ old('degree_coresponsible') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                    <option value="Dra." {{ old('degree_coresponsible') == 'Dra.' ? 'selected' : '' }}>Dra.</option>
                                </select>
                                
                                @error('degree_coresponsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                                <label for="other_coresponsible_name" class="block font-bold mb-2">Nombre completo del(la) corresponsable, comenzando por nombre y después apellidos.<span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el grado y nombre completo del académico comenzando por apellidos, por ejemplo Mtro. Eric Romero Martínez.">?</span></label>
                                <input type="text" name="other_coresponsible_name" id="other_coresponsible_name" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_coresponsible_name') border-red-500 @enderror" value="{{ old('other_coresponsible_name') }}">
                                @error('other_coresponsible_name')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-other-coresponsible-name">250</span></p>
                                
                                <label for="email_coresponsible" class="block font-bold mb-2">Correo electrónico del académico(a) <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escribe el correo electrónico del académico. Se generará una nueva contraseña y se le enviará por correo electrónico para dar seguimiento a la publicación de su evento.">?</span></label>
                                <input type="text" name="email_coresponsible" id="email_coresponsible" class="w-full form-input dark:bg-gray-800 dark:text-white @error('email_coresponsible') border-red-500 @enderror" value="{{ old('email_coresponsible') }}">
                                @error('email_coresponsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror

                                {{-- <input type="checkbox" name="external_coresponsible" class="my-4 input-checkbox dark:bg-gray-800 dark:text-white" ><label for="external_coresponsible" class="ml-2 font-bold mb-2">Selecciona esta opción si el académico es externo a la entidad</label>
                                @error('external_coresponsible')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror --}}
                            </div>
                        </div>

                        <!-- Resumen -->
                        <div class="mb-4">
                            <label for="summary" class="block font-bold mb-2">Resumen: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Agrega un resumen y/o información adicional para el público interesado en el evento, como máximo se admiten 500 caracteres.">?</span></label>
                            <textarea name="summary" id="summary" maxlength="500" rows="4" class="w-full form-textarea dark:bg-gray-800 dark:text-white @error('summary') border-red-500 @enderror" required>{{ old('summary') }}</textarea>
                            @error('summary')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-summary">500</span></p>
                        </div>

                        <!-- Correo de contacto -->
                        <div class="mb-4">
                            <label for="contact_email" class="block dark:text-gray-300 font-bold mb-2">Correo de contacto: <span class="text-sm">(campo opcional)</span><span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Correo electrónico público al que se puede solicitar mayor información.">?</span></label>
                            <input type="email" name="contact_email" id="contact_email" class="w-full form-input dark:bg-gray-800 dark:text-white @error('contact_email') border-red-500 @enderror" value="{{ old('contact_email') }}">
                            
                            @error('contact_email')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sitio web -->
                        <div class="mb-4">
                            <label for="website" class="block dark:text-gray-300 font-bold mb-2">Sitio web: <span class="text-sm">(campo opcional, comience con http o https)</span><span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Si cuenta con un sitio web específico del evento, puede difundirlo a través de este enlace.">?</span></label>
                            <input type="text" name="website" id="website" class="w-full form-input dark:bg-gray-800 dark:text-white @error('website') border-red-500 @enderror" value="{{ old('website') }}" placeholder="http://...">
                            
                            @error('website')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Requisitos adicionales -->
                        <div class="mb-4">
                            <label for="requirements" class="block font-bold mb-2">Requisitos adicionales: 
                                <span class="text-sm">(campo opcional, información adicional sobre el uso del espacio)</span>
                                <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Si requieres algo adicional como un acomodo de sillones, mesas, sillas, ect. descríbelo en este apartado.">?</span></label>
                            <textarea name="requirements" id="requirements" maxlength="500" rows="4" class="w-full form-textarea dark:bg-gray-800 dark:text-white @error('requirements') border-red-500 @enderror">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 dark:text-gray-300 text-sm">Caracteres restantes: <span id="char-count-requirements">500</span></p>
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
                        <div class="mb-4">
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
                        </div>

                        <!-- Modalidad -->
                        <div class="mb-4">
                            <label for="modality" class="block font-bold mb-2">Modalidad: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="<strong>A distancia:</strong> Modalidad de enseñanza-aprendizaje no presencial. Emplea medios de comunicación remota entre el alumnado y la profesora o el profesor.
                                                    <br><strong>Presencial:</strong> Se refiere a la actividad de impartir clase a un grupo de estudiantes en instalaciones universitarias, estando presentes tanto la profesora o el profesor, así como el alumnado.
                                                    <br><strong>Mixta: </strong>Es una forma híbrida en la cual la modalidad educativa presencial se mezcla con multimedios que facilitan el aprendizaje de los estudiantes a su propio ritmo, con altos grados de flexibilidad y sin restricción de tiempo ni espacio.
                                ">?</span>
                            </label>
                            <select name="modality" id="modality" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('modality') border-red-500 @enderror" required>
                                <option value="">Selecciona la modalidad</option>
                                <option value="Presencial" {{ old('modality') == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                                <option value="En línea" {{ old('modality') == 'En línea' ? 'selected' : '' }}>En línea</option>
                                <option value="Mixta" {{ old('modality') == 'Mixta' ? 'selected' : '' }}>Mixta</option>
                            </select>
                            @error('modality')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Alcance -->
                        <div class="mb-4">
                            <label for="scope" class="block font-bold mb-2">Alcance: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="Selecciona el alcance del evento.">?</span>
                            </label>
                            <select name="scope" id="scope" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('scope') border-red-500 @enderror" required>
                                <option value="">Selecciona el alcance</option>
                                <option value="Nacional" {{ old('scope') == 'Nacional' ? 'selected' : '' }}>Nacional</option>
                                <option value="Internacional" {{ old('scope') == 'Internacional' ? 'selected' : '' }}>Internacional</option>
                            </select>
                            @error('scope')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de proyecto -->
                        <div class="mb-4">
                            <label for="project_type" class="block font-bold mb-2">Tipo de proyecto: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="<strong>Abierto: </strong>Dirigido a población en general.
                                                    <br><strong>Cerrado: </strong>Convenio con sector público, convenio con sector privado o bases de colaboración con UNAM.
                                    ">?</span>
                            </label>
                            <select name="project_type" id="project_type" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('project_type') border-red-500 @enderror" required>
                                <option value="">Selecciona el tipo de proyecto</option>
                                <option value="Abierto" {{ old('project_type') == 'Abierto' ? 'selected' : '' }}>Abierto</option>
                                <option value="Cerrado" {{ old('project_type') == 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                            @error('project_type')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Equidad de genero -->
                        <div class="mb-4">
                            <label for="gender_equality" class="block font-bold mb-2">¿La actividad refiere a equidad de género o no discriminación?: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600"
                                data-tippy-content="<strong>No: </strong>El tema central del evento es ajeno a la equidad de género.
                                                    <br><strong>Equidad de género: </strong>Establecimiento y fortalecimiento de mecanismos destinados a impulsar la igualdad de derechos, responsabilidades y oportunidades de mujeres y hombres; revalorar el papel de la mujer y del hombre en el seno familiar, y en los ámbitos institucional y social; eliminar la discriminación individual y colectiva hacia el hombre y la mujer u otras minorías. 
                                                    <br><strong>Estadísticas desagregadas por sexo: </strong>Son fuentes de información cuantitativa diseñadas para visibilizar la situación de las mujeres con relación a los hombres, en un determinado contexto social o institucional, a lo largo del tiempo. 
                                                    <br><strong>Género: </strong>Conjunto de ideas, creencias y atribuciones sociales construidas en cada cultura y momento histórico, tomando como base la diferencia sexual; a partir de ello se construyen los conceptos de “masculinidad” y “feminidad”, los cuales determinan el comportamiento, las funciones, oportunidades, valoración y las relaciones entre hombres y mujeres. El concepto alude a las formas históricas y socioculturales en que mujeres y hombres construyen su identidad, interactúan y organizan su participación en la sociedad. 
                                                    <br><strong>Igualdad de género : </strong>Situación en la que mujeres y hombres tienen las mismas posibilidades u oportunidades en la vida de acceder y controlar recursos y bienes valiosos desde el punto de vista social. El objetivo no es tanto que mujeres y hombres sean iguales, sino conseguir que unos y otros tengan las mismas oportunidades en la vida.
                                    ">?</span>
                            </label>
                            <select name="gender_equality" id="gender_equality" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('gender_equality') border-red-500 @enderror" required>
                                <option value="">Selecciona una opcion</option>
                                <option value="No" {{ old('gender_equality') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Equidad de género" {{ old('gender_equality') == 'Equidad de género' ? 'selected' : '' }}>Equidad de género</option>
                                <option value="Estadísticas desagregadas por sexo" {{ old('gender_equality') == 'Estadísticas desagregadas por sexo' ? 'selected' : '' }}>Estadísticas desagregadas por sexo</option>
                                <option value="Género" {{ old('gender_equality') == 'Género' ? 'selected' : '' }}>Género</option>
                                <option value="Igualdad de género" {{ old('gender_equality') == 'Igualdad de género' ? 'selected' : '' }}></option>
                            </select>
                            @error('gender_equality')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Area de conocimiento -->
                        <div class="mb-4">
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
                        </div>

                        <!-- Banner del evento -->
                        <div class="mb-4">
                            <label for="cover_image" class="block font-bold mb-2">Cartel publicitario (imagen en formato horizontal). 
                                <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Esta imagen será utilizada para mostrar en la cartelera, debe ser breve y atractiva para el público interesado. Solo se admiten los formatos .jpg, .jpeg y .png con un peso máximo de 5 MB">?</span>
                                <br>En caso de no contar con el cartel del evento, puede agregarlo en otro momento.
                            </label>
                            <input 
                                type="file" 
                                name="cover_image" 
                                id="cover_image"                                 
                                accept=".jpg, .jpeg, .png"
                                maxlength="5242880"
                                class="form-input dark:bg-gray-800 dark:text-white @error('cover_image') border-red-500 @enderror">
                            @error('cover_image')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Programa -->
                        {{-- <div class="mb-4">
                            <label for="program" class="block font-bold mb-2">Cartel o programa: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Suba un documento con mayor información sobre el evento, puede ser un cartel o el programa. Solo se admite el formato pdf con un peso máximo de 5 MB">?</span></label>
                            <input
                                type="file"
                                name="program" 
                                id="program"
                                accept=".pdf"
                                maxlength="5242880"
                                class="form-input dark:bg-gray-800 dark:text-white @error('program') border-red-500 @enderror">
                            @error('program')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}

                        <!-- Se requiere registro -->
                        <div class="mb-4">
                            <label for="registration_required" class="block font-bold mb-2">Requiere Registro: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Si requiere un registro de los asistentes al evento, active esta casilla y posteriormente escriba la URL del enlace web donde se pueden registrar los asistentes. Este sitio de registro debe ser gestionado por el responsable del evento.">?</span></label>
                            <input type="checkbox" name="registration_required" id="registration_required" class="form-checkbox" {{ old('registration_required') ? 'checked' : '' }}>
                            @error('registration_required')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Enlace del registro -->
                        <div id="registration_url_container" class="mb-4 hidden">
                            <label for="registration_url" class="block font-bold mb-2">URL de Registro: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Escriba la URL del sitio de registro del sistema.">?</span></label>
                            <input type="text" name="registration_url" id="registration_url" class="form-input dark:bg-gray-800 dark:text-white @error('registration_url') border-red-500 @enderror" value="{{ old('registration_url') }}">
                            @error('registration_url')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror                            
                        </div>

                        <div class="border p-4 border-gray-800 dark:border-gray-300">
                            <b>Nota:</b> Si requiere servicio de grabación, fotografia o transmisión, por favor acuda directamente a UDEMAT para solicitar el servicio.
                        </div>

                        {{-- Se remueven las opciones de servicios de UDEMAT hasta que esté implementado --}}

                        {{-- <div class="mb-4">
                            <label for="transmission_required" class="block font-bold mb-2">Requiere transmisión: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Si requiere que su evento sea transmitido por los canales institucionales gestionados por UDEMAT, active esta casilla.">?</span></label>
                            <input type="checkbox" name="transmission_required" class="form-checkbox">
                            @error('transmission_required')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror                            
                        </div>
                        
                        <div class="mb-4">
                            <label for="recording_required" class="block font-bold mb-2">Requiere grabación: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="Si requiere que se lleve a cabo la grabación de su evento, active esta casilla.">?</span></label>
                            <input type="checkbox" name="recording_required" class="form-checkbox">
                            @error('recording_required')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div> --}}
                        
                        <div class="flex">
                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Registrar Evento</button>
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





