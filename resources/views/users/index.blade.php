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

        <div>
            <a href="{{ route('users.create') }}" class="block text-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 inline-block">Agregar usuario</a>
        </div>
            
        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead class="bg-gray-50 dark:bg-gray-600">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Número de trabajador
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Correo Electrónico
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Adscripción(es)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Rol(es)
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:bg-gray-700">
                @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->doi }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->degree }} {{ $user->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($user->adscriptions as $adscription)
                                {{ $adscription->department->name }}<br>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @foreach ($user->roles as $role)
                                {{ $role->name }}<br>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('users.edit', $user) }}" class="text-indigo-600 dark:text-indigo-300 hover:underline">Editar</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="my-4">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-red-500 text-white font-semibold rounded-md">Regresar</a>
        </div>

    </div>
</x-app-layout>
