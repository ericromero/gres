<x-guest-layout class="min-h-screen flex items-center justify-center">
    <!-- Session Status -->
    <x-auth-session-status class="mb-1" :status="session('status')" />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 w-full max-w-4xl p-6 bg-white shadow-lg rounded-lg">
        <div class="p-2">
            <a href="{{route('eventos.cartelera')}}"><img src="{{ asset('images/logo.png') }}" alt="G-RES" class="w-full sm:max-w-md shadow-md overflow-hidden sm:rounded-lg" /></a>
        </div>

        <div class="mt-4"> <!-- Formulario de logueo -->
            <div>
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password" class="block mt-1 w-full"
                                        type="password"
                                        name="password"
                                        required autocomplete="current-password" />

                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Remember Me -->
                    <div class="block mt-4">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
                        </label>
                    </div>

                    <div class="block mt-4">                
                        <x-primary-button>
                            {{ __('Log in') }}
                        </x-primary-button>
                    </div>

                    <div class="block mt-4">                
                        <a href="{{ route('eventos.cartelera') }}" class="inline-block px-4 py-2 mt-4 bg-blue-500 hover:bg-blue-700 hover:text-white text-white font-semibold text-sm rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Regresar
                        </a>
                    </div>

                </form>
            </div>
        </div>   
    </div>

    <div class="flex items-center mt-4">
        @if (Route::has('password.request'))
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                {{ __('Forgot your password?') }}
            </a>
        @endif
    </div>

</x-guest-layout>
