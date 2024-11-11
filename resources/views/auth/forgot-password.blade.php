<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 w-full max-w-4xl p-6 bg-white shadow-lg rounded-lg">
        <div class="p-2">
            <a href="{{route('eventos.cartelera')}}"><img src="{{ asset('images/logo.png') }}" alt="G-RES" class="w-full sm:max-w-md shadow-md overflow-hidden sm:rounded-lg" /></a>
        </div>

        <div class="mt-4">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Email Password Reset Link') }}
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
</x-guest-layout>
