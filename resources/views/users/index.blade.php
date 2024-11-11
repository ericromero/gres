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

        <div class="my-2 flex">
            <a href="{{ route('users.create') }}" class="px-4 py-2 bg-green-500 text-white rounded-md">Agregar usuario</a>
            <form action="{{ route('users.list') }}" method="GET">
                @csrf
                <input type="submit" name="list" id="list" value="Ver lista completa" class="mx-2 px-4 py-2 bg-blue-500 text-white rounded-md cursor-pointer">
            </form>            
        </div>

        <form action="{{route('users.search')}}" method="POST">
            <div class="my-2 p-4 border border-gray-300">            
                @csrf
                <div>
                    <label for="user">Búsqueda de usuario usuario</label>
                    <select name="user" id="user" class="js-example-basic-single" required>
                        <option value="">Ingresa el nombre y/o apellidos del usuario a buscar</option>
                        @foreach ($users as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <input type="submit" value="Buscar académico" class="block text-center px-3 py-2 bg-orange-500 text-white rounded-md hover:bg-orange-600 inline-block cursor-pointer">
                </div>
            </div>
        </form>
        

        <div class="my-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-500 text-white rounded-md">Regresar</a>
        </div>

    </div>
</x-app-layout>

<script>
    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });
</script>
