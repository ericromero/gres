<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Lista de Usuarios') }}
        </h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Código para el manejo de notificaciones --}}
        @if(session('success'))
            <div class="bg-green-200 text-green-800 p-4 mb-2 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{route('users.list')}}" method="GET">
            @csrf
            <div class="my-2 p-4 border border-gray-300">
                <h3 class="font-semibold text-lg">Filtro de búsqueda</h3>
                <!-- Filtrar por nombre -->
                <div>
                    <label for="user">Nombre y/o apellidos</label>
                    <input type="text" name="name">
                </div>

                <!-- Seleccionar los roles a filtrar -->
                <div class="my-2">
                    @foreach ($roles as $role)      
                        <label class="mr-3">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}">
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
                
                <div class="my-2">
                    <input type="submit" value="Buscar académico" class="block text-center px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 inline-block cursor-pointer">
                </div>
            </div>
        </form>
        
        <!-- Despliega tabla de usuarios -->
        <div class="p-2 m-2">
            <table class="border border-gray-700 dark:border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-700 dark:border-gray-300">Nombre</th>
                        <th class="border border-gray-700 dark:border-gray-300">Correo electrónico</th>
                        <th class="border border-gray-700 dark:border-gray-300">Adscripción</th>
                        <th class="border border-gray-700 dark:border-gray-300">Rol</th>
                        <th class="border border-gray-700 dark:border-gray-300">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                    <tr>
                        <td class="border border-gray-700 dark:border-gray-300">{{ $user->degree }} {{ $user->name }}</td>
                        <td class="border border-gray-700 dark:border-gray-300">{{ $user->email }}</td>
                        <td class="border border-gray-700 dark:border-gray-300">
                            @if($user->departments!=null)
                                <ul>
                                    @forelse ($user->departments as $department)
                                        <li>
                                            {{ $department->name }}
                                        </li>
                                    @empty
                                        Sin adscripción
                                    @endforelse
                                </ul>
                            @endif
                        </td>
                        <td class="border border-gray-700 dark:border-gray-300">
                            @foreach ($user->roles as $role)
                                <span>{{ $role->name }}</span>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </td>
                        <td class="border border-gray-700 dark:border-gray-300"></td>
                    </tr>
                    @empty
                        <th colspan="5">No hay usuarios en el sistema</th>
                    @endforelse

                </tbody>
            </table>
        </div>

        <div>
            {{ $users->links() }}
        </div>

        <div class="my-4">
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
        </div>

    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
