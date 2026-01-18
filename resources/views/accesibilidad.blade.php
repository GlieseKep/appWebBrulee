@extends('layouts.app')

@section('title', 'Brúlée — Accesibilidad')

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Accesibilidad</h1>
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="login-card p-4">
                    <h2 class="mb-4">Opciones de Accesibilidad</h2>

                    <p>Nuestra aplicación incluye las siguientes características de accesibilidad:</p>

                    <ul>
                        <li><strong>Alto Contraste:</strong> Activa un modo de alto contraste para mejorar la legibilidad
                        </li>
                        <li><strong>Ajuste de Tamaño de Texto:</strong> Aumenta o disminuye el tamaño del texto según tus
                            necesidades</li>
                        <li><strong>Ocultar Imágenes:</strong> Opción para mostrar solo texto alternativo de las imágenes
                        </li>
                        <li><strong>Navegación por Teclado:</strong> Todas las funcionalidades son accesibles mediante
                            teclado</li>
                    </ul>

                    <p class="mt-3">
                        Puedes acceder a estas opciones en cualquier momento haciendo clic en el botón de accesibilidad
                        <i class="bi bi-universal-access"></i> en la barra de navegación.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection