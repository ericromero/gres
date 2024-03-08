<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Recursos de') }} {{ $department->name }}
        </h2>
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

        <div class="grid grid-cols-1 md:grid-cols-2">
            <!-- Sección con la nueva lista de recursos -->
            <div class="border border-gray-700 dark:border-gray-200 p-4 m-2">
                <h2 class="font-semibold text-lg mb-2">Recursos Actuales</h2>
                @if ($department->resources->isNotEmpty())
                    <table class="border border-gray-700 dark:border-gray-300">
                        <thead class="bg-gray-300 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2">Recurso</th>
                                <th class="px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($department->resources as $resource)
                                <tr>
                                    <td class="px-4 py-2 {{ $resource->active ? '' : 'text-red-700 dark:text-red-300' }}">{{ $resource->name }}</td>
                                    <td class="px-4 py-2">
                                        <form action="{{ route('resource.toggleStatus', $resource->id) }}" method="post" class="inline-block">
                                            @csrf
                                            <button type="submit" class="{{ $resource->active ? 'text-blue-800 dark:text-blue-300' : 'text-green-700 dark:text-green-300' }} hover:underline focus:outline-none">
                                                {{ $resource->active ? 'Deshabilitar' : 'Habilitar' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('resource.delete', $resource->id) }}" method="post" class="inline-block" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este recurso? Esta acción borrará todo su historial de uso en el sistema.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-800 dark:text-red-300 hover:underline focus:outline-none ml-2">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No hay recursos disponibles para este espacio.</p>
                @endif
            </div>
            
            <!-- Sección para registrar un nuevo recurso -->
            <div class="border border-gray-700 dark:border-gray-200 p-4 m-2">
                <div class="p-2">
                    <h2 class="font-semibold text-lg mb-2">Agregar recurso</h2>        
                    <form action="{{ route('department.addResources', $department->id) }}" method="POST" >
                        @csrf

                        <!-- Nombre del curso -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Nombre del recurso:</label>
                            <input type="text" name="name" id="name" class="form-input dark:bg-gray-800 dark:text-white @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                            @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Número de inventario -->
                        <div class="mb-4">
                            <label for="inventory" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Número de inventario: (campo opcional)</label>
                            <input type="text" name="inventory" id="inventory" class="form-input dark:bg-gray-800 dark:text-white @error('inventory') border-red-500 @enderror" value="{{ old('inventory') }}">
                            @error('inventory')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de recurso -->
                        <div class="mb-4">
                            <label for="resource_type_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Tipo de recurso: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="En caso de no encontrar el tipo de recurso adecuado, selecciona la opción Otro e ingresa la información correspondiente.">?</span></label>
                            <select name="resource_type_id" id="resource_type_id" class="js-example-basic-single dark:bg-gray-800 dark:text-white @error('resource_type_id') border-red-500 @enderror" required>
                                <option value="">Seleccionar tipo de recurso</option>
                                @foreach($resourcetypes as $type)
                                    <option value="{{ $type->id }}" {{ old('resource_type_id') == $type->id ? 'selected' : '' }}>{{ $type->type }}</option>
                                @endforeach
                                <option value="other">Otro</option>
                            </select>
                            @error('resource_type_id')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Campo para agregar otro tipo de recurso -->
                        <div class="mb-4" id="other-resource-container" style="{{ $errors->has('resource_type_id') ? 'display: block;' : 'display: none;' }}">
                            <label for="other_resource" class="block dark:text-gray-300 font-bold mb-2">Indica el tipo de recurso: <span class="px-1 text-gray-600 bg-gray-300 dark:text-gray-300 dark:bg-gray-600" data-tippy-content="No debe exceder los 250 caracteres incluyendo espacios en blanco.">?</span></label>
                            <input type="text" name="other_resource" id="other" maxlength="250" class="w-full form-input dark:bg-gray-800 dark:text-white @error('other_resource') border-red-500 @enderror" value="{{ old('other_resource') }}">
                            @error('other_resource')
                                <p class="text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>


                        <div class="p-4">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Agregar recurso</button>
                        </div>
                    </form>
            </div>
            </div>
        </div>

        <div class="mt-2 ml-2">
            <a href="{{ route('departments.index') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
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
        // Manejador de eventos para el cambio en la selección de Tipo de recurso
        $("#resource_type_id").change(function () {
            // Muestra u oculta el campo de entrada adicional según la opción seleccionada
            $("#other-resource-container").toggle($(this).val() === "other");
        });

        // Agregamos un evento adicional para manejar la visibilidad inicial basada en el valor seleccionado
        $("#resource_type_id").trigger('change');

        // Manejador de eventos para la entrada en el campo de entrada adicional (otro tipo de recurso)
        $("#other_resource").on('input', function () {
            // Actualiza el conteo de caracteres restantes
            const charCount = 250 - $(this).val().length;
            $("#char-count-other-resource").text(charCount);
        });
    });
</script>
