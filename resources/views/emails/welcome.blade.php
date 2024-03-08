@component('mail::message')
{{-- Imagen --}}

![Logo]({{ asset('images/50anios.png') }})


# ¡Bienvenido(a)!

Se ha creado una cuenta para ti en la Cartelera - Psicología. Para acceder al sistema utiliza los siguientes datos:

- Nombre de usuario: {{ $email }}
- Contraseña: {{ $password }}

Puedes modificar tu contraseña desde la opción "Perfil" del sistema.

Para acceder al sistema, da clic en el siguiente botón.
@component('mail::button', ['url' => url('/login')])
Iniciar Sesión
@endcomponent

{{-- Salutation --}}
Saludos,<br>
Cartelera de eventos de la Facultad de Psicología de la UNAM.

{{-- Subcopy --}}
@component('mail::subcopy')
Si tienes problemas al hacer clic en el botón "Iniciar Sesión", copia y pega esta URL en tu navegador web: [{{ url('/login') }}]({{ url('/login') }})
@endcomponent

@endcomponent
