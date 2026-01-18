<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Brúlée Catering & Event Design')</title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

    @stack('styles')
</head>

<body>

    <div class="contenedor">
        <!-- CABECERA -->
        <div id="Cabecera" class="Pagina">
            <img id="Cabecera-logo" src="{{ asset('assets/img/Logo.png') }}" alt="Logo brûlée">
            @yield('cabecera-text')
        </div>

        <!-- NAVBAR -->
        @include('partials.navbar')

        <!-- CONTENIDO PRINCIPAL -->
        <main>
            @yield('content')
        </main>

        <!-- FOOTER -->
        @include('partials.footer')
    </div>

    <!-- OFFCANVAS ACCESIBILIDAD -->
    @include('partials.offcanvas-accesibilidad')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de accesibilidad -->
    <script src="{{ asset('js/accesibilidad.js') }}"></script>

    @stack('scripts')

    <script>
        // Configurar AJAX con CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inicializar accesibilidad
        document.addEventListener("DOMContentLoaded", () => {
            new controlarAccesibilidad();
        });
    </script>
</body>

</html>