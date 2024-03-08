<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Agregar Nuevo Departamento') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <form method="POST" action="{{ route('departments.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="name" class="block">Nombre corto o alias del Departamento:</label>
                <input type="text" name="name" id="name" class="form-input dark:bg-gray-800 dark:text-white rounded-md @error('name') border-red-500 @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subir nuevo logo -->
            <div class="mb-4">
                <label for="logo" class="block text-gray-600">Nuevo Logo: (Máximo 2 MB y formato png o jpg) <br>Puede dejar este campo en blanco y se agregará por omisión el logo de la UNAM.</label>
                <input type="file" name="logo" id="logo" class="form-input dark:bg-gray-800 dark:text-white rounded-md" accept="image/png, image/jpg">
                @error('logo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block">Nombre completo del departamento</label>
                <textarea name="description" id="description" class="form-textarea dark:bg-gray-800 dark:text-white rounded-md @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="institution_id" class="block">Institución:</label>
                <select name="institution_id" id="institution_id" class="form-select dark:bg-gray-800 dark:text-white rounded-md" required>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                    @endforeach
                </select>
                @error('institution_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="responsible_id" class="block">Responsable:</label>
                <select name="responsible_id" id="responsible_id" class="form-select js-example-basic-single dark:bg-gray-800 dark:text-white rounded-md" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('responsible_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Guardar Departamento</button>
                <a href="{{ route('departments.index') }}" class="px-4 ml-2 py-2 bg-red-500 text-white font-semibold rounded-md">Cancelar registro</a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>