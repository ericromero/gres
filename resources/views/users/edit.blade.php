<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Actualizar información')}} de {{$user->name}}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Nombre completo: <span class="text-sm text-gray-700 dark:text-gray-300">(Comenzando por nombre seguido de apellidos)</span></label>
                <input type="name" name="name" id="name" value="{{ $user->name }}" class="form-input dark:bg-gray-800 dark:text-white">
                @error('name')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>   

            <div class="mb-4">
                <label for="degree" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Grado académico actual: {{$user->degree}}</label>
                <select name="degree" id="degree" class="form-input dark:bg-gray-800 dark:text-white" required>
                    <option value="{{$user->degree}}" selected>{{ $user->degree }}</option>
                    <option value="C.">C.</option>
                    <option value="Sra.">Sra.</option>
                    <option value="Sr.">Sr.</option>
                    <option value="Lic.">Lic.</option>
                    <option value="Mtra.">Mtra.</option>
                    <option value="Mtro.">Mtro.</option>
                    <option value="Dra.">Dra.</option>
                    <option value="Dr.">Dr.</option>
                </select>
            </div> 

            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Correo electrónico:</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-input dark:bg-gray-800 dark:text-white">
                @error('email')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>                        

            <div class="mb-4">
                <label for="password" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Contraseña</label>
                <input type="password" name="password" id="password" class="form-input dark:bg-gray-800 dark:text-white" placeholder="Ingrese nueva contraseña">
                @error('password')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>
        
            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Confirme la contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input dark:bg-gray-800 dark:text-white" placeholder="Confirme la contraseña">
            </div>
        
            <div class="mb-4">
                <label for="departments" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Departamentos de Adscripción:</label>
                @foreach($departments as $department)
                    <label class="inline-flex items-center mt-1">
                        <input type="checkbox" name="departments[]" value="{{ $department->id }}" {{ in_array($department->id, old('departments', $user->adscriptions->pluck('department_id')->toArray())) ? 'checked' : '' }} class="form-checkbox">
                        <span class="mr-4 ml-1 dark:bg-gray-800 dark:text-white">{{ $department->name }}</span>
                    </label>
                @endforeach
                @error('departments')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="roles" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Funciones del usuario</label>
                @foreach($roles as $role)
                    <label class="inline-flex items-center mt-1">
                        <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }} class="form-checkbox">
                        <span class="mr-4 ml-1 dark:bg-gray-800 dark:text-white">{{ $role->name }}</span>
                    </label>
                @endforeach
                @error('roles')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mt-4 mb-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-md">Guardar cambios</button>
                <a href="{{ route('users.index') }}" class="ml-4 px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Cancelar cambios</a>
            </div>            
        </form>
    </div>
</x-app-layout>
