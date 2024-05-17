<x-app-layout>
    <x-slot name="header">
        <h2 class="p-2 font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200 {{ $space->availability ? 'bg-green-300' : 'bg-red-300' }}">
            {{ __('Editar Espacio') }}
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

        <div class="p-2 m-2 border border-gray-600 dark:border-gray-200">
            <form action="{{ route('spaces.update', $space->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Nombre del Espacio:</label>
                    <input type="text" name="name" id="name" class="form-input dark:bg-gray-800 dark:text-white @error('name') border-red-500 @enderror" value="{{ old('name', $space->name) }}" required>
                    @error('name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="location" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Ubicación:</label>
                    <input type="text" name="location" id="location" class="form-input dark:bg-gray-800 dark:text-white @error('location') border-red-500 @enderror" value="{{ old('location', $space->location) }}" required>
                    @error('location')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="capacity" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Capacidad:</label>
                    <input type="number" name="capacity" id="capacity" class="form-input dark:bg-gray-800 dark:text-white @error('capacity') border-red-500 @enderror" value="{{ old('capacity', $space->capacity) }}" required>
                    @error('capacity')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="department_id" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Departamento:</label>
                    <select name="department_id" id="department_id" class="form-select js-example-basic-single  @error('department_id') border-red-500 @enderror" required>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $space->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="photography" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Fotografía del Espacio:</label>
                    <input type="file" name="photography" id="photography" class="form-input dark:bg-gray-800 dark:text-white @error('photography') border-red-500 @enderror">
                    @error('photography')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Guardar cambios</button>
                    <a href="{{ route('spaces.index') }}" class="px-4 py-2 ml-4 bg-red-500 text-white font-semibold rounded-md">Cancelar cambios</a>
                </div>
            </form>
        </div>

        <!-- Bloque para habilitar o inhabilitar un espacio -->
        <div class="p-2 m-2 border border-gray-600 dark:border-gray-200">
            <h3 class="font-bold mb-2">Disponibilidad del espacio</h3>

            <form action="{{ route('spaces.toggleAvailability', $space->id) }}" method="POST">
                @csrf
                @method('PUT')
                <label for="sumbit">Actualmente el espacio está <b>{{ $space->availability ? 'Disponible' : 'Inhabilitado' }}</b>, para cambiar el estado da clic en el siguiente botón</label>
                <button type="submit" class="{{ $space->availability ? 'bg-red-500 text-white p-2' : 'bg-green-500 text-white p-2' }}">
                    {{ $space->availability ? 'Inhabilitar el espacio' : 'Habilitar el espacio' }}
                </button>
            </form>
        </div>
        
        <!-- Sección de Excepciones de Horario -->
        <div class="p-2 m-2 border border-gray-600 dark:border-gray-200">
            <h3 class="font-bold mb-2">Excepciones de Horario</h3>

            {{-- Listado de Excepciones Actuales --}}
            
            <ul class="p-2">
            @forelse ($space->exceptions as $exception)                
                    <li>
                    {{-- Botón para eliminar la excepción --}}
                    <form action="{{ route('spaces.exceptions.destroy', [$space->id, $exception->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <label for="submit">{{ $exception->day_of_week }}: {{ $exception->start_time }} - {{ $exception->end_time }}</label>
                        <button type="submit" class="text-red-500">Eliminar</button>
                    </form>
                    </li>
                
            @empty
                <p>No hay excepciones registradas.</p>
            @endforelse
            </ul>

            {{-- Formulario para Agregar Nuevas Excepciones --}}
            <h3 class="font-semibold mb-2">Agregar excepción</h3>
            <form action="{{ route('spaces.exceptions.store', $space->id) }}" method="POST">
                @csrf
                <div class="m-1">
                    <label for="day_of_week">Día de la semana:</label>
                    <select name="day_of_week" required>
                        <option value="Lunes">Lunes</option>
                        <option value="Martes">Martes</option>
                        <option value="Miércoles">Miércoles</option>
                        <option value="Jueves">Jueves</option>
                        <option value="Viernes">Viernes</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>
                <div class="m-1">
                    <label for="start_time">Hora de inicio:</label>
                    <input type="time" name="start_time" required>
                
                    <label class="ml-2" for="end_time">Hora de termino:</label>
                    <input type="time" name="end_time" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2">Agregar Excepción</button>
            </form>
        </div>

        <!-- Actualización de documento de lineamientos -->
        <div class="p-2 m-2 border border-gray-600 dark:border-gray-200">
            <h3 class="font-bold mb-2">Lineamientos del espacio</h3>
            <div class="p-2">
                @if(isset($space->terms)&&$space->terms!=null)
                    <a href="{{ asset($space->terms) }}" target="_blank">Ver documento Actual</a>
                @else
                    <div class="p-2 text-red-600 dark:text-red-300">No se ha cargado el documento de lineamientos.</div>
                @endif
            </div>
            <div class="p-2">
                <form action="{{ route('spaces.updateTerms', $space) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Carga del documento pdf -->
                    <input 
                        type="file" 
                        name="terms" 
                        id="terms"                                 
                        accept=".pdf"
                        required
                        class="form-input dark:bg-gray-800 dark:text-white @error('terms') border-red-500 @enderror">
                    @error('terms')
                        <p class="text-red-500 text-sm">{{ $message }}</p>
                    @enderror
                
                    <button type="submit" class="bg-blue-500 text-white p-2">Actualizar documento</button>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>