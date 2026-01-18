@extends('layouts.app')

@section('title', 'Brúlée Catering & Event Design — Home')

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Catálogo</h1>
@endsection

@section('content')
    {{-- Mensajes de sesión --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Carrusel --}}
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" aria-label="Carrusel de promociones">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="{{ asset('assets/img/carrusel1.jpg') }}" class="d-block w-100" alt="Promoción 1">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/img/carrusel2.jpg') }}" class="d-block w-100" alt="Promoción 2">
            </div>
            <div class="carousel-item">
                <img src="{{ asset('assets/img/carrusel3.jpg') }}" class="d-block w-100" alt="Promoción 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev"
            aria-label="Anterior">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next"
            aria-label="Siguiente">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    {{-- Sección Nuestros Productos --}}
    <nav class="sub-navbar" aria-label="Sección de productos">
        <div class="container text-center py-2">
            <h2 class="m-0">Nuestros Productos</h2>
        </div>
    </nav>
    <section class="sub-navbar-separacion" aria-hidden="true"></section>

    {{-- Productos Favoritos --}}
    <div class="container">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            {{-- Producto 1 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/gatoGordo.jpg') }}" class="card-img-top" alt="Gato Gordo Roll">
                    <div class="card-body">
                        <h5 class="card-title">Gato Gordo Roll</h5>
                        <p class="card-text">Delicioso pastel enrollado con sabor a chocolate</p>
                        <p class="fw-bold">$12.50</p>
                    </div>
                </div>
            </div>

            {{-- Producto 2 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/julieta.jpg') }}" class="card-img-top" alt="Julieta">
                    <div class="card-body">
                        <h5 class="card-title">Julieta</h5>
                        <p class="card-text">Elegante pastel decorado con flores comestibles</p>
                        <p class="fw-bold">$25.00</p>
                    </div>
                </div>
            </div>

            {{-- Producto 3 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/macarron-de-fresa.jpg') }}" class="card-img-top" alt="Macarrón de Fresa">
                    <div class="card-body">
                        <h5 class="card-title">Macarrón de Fresa</h5>
                        <p class="card-text">Macarrones franceses con relleno de fresa natural</p>
                        <p class="fw-bold">$8.00</p>
                    </div>
                </div>
            </div>

            {{-- Producto 4 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/box-corona.jpg') }}" class="card-img-top" alt="Box Corona">
                    <div class="card-body">
                        <h5 class="card-title">Box Corona</h5>
                        <p class="card-text">Caja de regalo con variedad de postres gourmet</p>
                        <p class="fw-bold">$35.00</p>
                    </div>
                </div>
            </div>

            {{-- Producto 5 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/amelie.jpg') }}" class="card-img-top" alt="Amelie">
                    <div class="card-body">
                        <h5 class="card-title">Amelie</h5>
                        <p class="card-text">Tarta francesa con frutas frescas de temporada</p>
                        <p class="fw-bold">$18.00</p>
                    </div>
                </div>
            </div>

            {{-- Producto 6 --}}
            <div class="col">
                <div class="card h-100">
                    <img src="{{ asset('assets/img/box-corona.jpg') }}" class="card-img-top" alt="Especial">
                    <div class="card-body">
                        <h5 class="card-title">Box Especial</h5>
                        <p class="card-text">Selección premium de nuestros mejores productos</p>
                        <p class="fw-bold">$45.00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="sub-navbar-separacion" aria-hidden="true"></section>

    {{-- Nuestros Servicios --}}
    <section class="sub-navbar sub-navbar-servicios text-center py-4" aria-label="Nuestros servicios">
        <h2 class="mb-3">Nuestros Servicios</h2>

        <div class="container">
            <div class="row justify-content-center g-4">

                <div class="col-6 col-md-3">
                    <a href="#" class="text-decoration-none d-block" aria-label="Ver servicio de organización de eventos">
                        <img src="{{ asset('assets/img/gatoGordo.jpg') }}" alt="Asesor de servicios"
                            class="img-fluid rounded-circle mb-2">
                        <p>Organización de eventos</p>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="#" class="text-decoration-none d-block" aria-label="Ver servicio de pastelería personalizada">
                        <img src="{{ asset('assets/img/box-corona.jpg') }}" alt="Pastel por pedido"
                            class="img-fluid rounded-circle mb-2">
                        <p>Pastelería personalizada</p>
                    </a>
                </div>

                <div class="col-6 col-md-3">
                    <a href="#" class="text-decoration-none d-block" aria-label="Ver servicio de catering gourmet">
                        <img src="{{ asset('assets/img/amelie.jpg') }}" alt="Imagen de catering"
                            class="img-fluid rounded-circle mb-2">
                        <p>Catering gourmet</p>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="sub-navbar-separacion" aria-hidden="true"></section>
@endsection

@push('scripts')
    <script>
        // Inicializar carrusel de Bootstrap
        const myCarouselElement = document.querySelector('#carouselExample');
        if (myCarouselElement) {
            const carousel = new bootstrap.Carousel(myCarouselElement, {
                interval: 3000,
                ride: 'carousel'
            });
        }
    </script>
@endpush