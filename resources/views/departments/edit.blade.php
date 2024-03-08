<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Editar Departamento') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <form method="POST" action="{{ route('departments.update', $department) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-600">Nombre del Departamento:</label>
                <input type="text" name="name" id="name" class="form-input dark:bg-gray-800 dark:text-white rounded-md @error('name') border-red-500 @enderror" value="{{ old('name', $department->name) }}" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description" class="block text-gray-600">Descripción:</label>
                <textarea name="description" id="description" class="form-textarea dark:bg-gray-800 dark:text-white rounded-md @error('description') border-red-500 @enderror" required>{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="institution_id" class="block text-gray-600">Institución:</label>
                <select name="institution_id" id="institution_id" class="form-select dark:bg-gray-800 dark:text-white rounded-md @error('institution_id') border-red-500 @enderror" required>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}" {{ old('institution_id', $department->institution_id) == $institution->id ? 'selected' : '' }}>{{ $institution->name }}</option>
                    @endforeach
                </select>
                @error('institution_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Logo actual -->
            <div class="mb-4">
                <label class="block text-gray-600">Logo Actual: {{ $department->logo }} </label>
                @if($department->logo)
                    @if($department->logo=='unam.png')
                        <img src="{{ asset('images/' . $department->logo) }}" alt="Logo Actual" class="w-20 h-20">
                    @else
                        <img src="{{ asset('images/logos/' . $department->logo) }}" alt="Logo Actual" class="w-20 h-20">
                    @endif
                @else
                    <p>No hay logo actual.</p>
                @endif
            </div>

            <!-- Subir nuevo logo -->
            <div class="mb-4">
                <label for="logo" class="block text-gray-600">Logo: (Máximo 2 MB y formato png o jpg) </label>
                <input type="file" name="logo" id="logo" class="form-input dark:bg-gray-800 dark:text-white rounded-md" accept="image/png, image/jpg">
                @error('logo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="responsible_id" class="block text-gray-600">Responsable:</label>
                <select name="responsible_id" id="responsible_id" class="form-select js-example-basic-single dark:bg-gray-800 dark:text-white rounded-md" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $user->id == $department->responsible_id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('responsible_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Guardar cambios</button>
                <a href="{{ route('departments.index') }}" class="px-4 ml-2 py-2 bg-red-500 text-white font-semibold rounded-md">Cancelar cambios</a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>