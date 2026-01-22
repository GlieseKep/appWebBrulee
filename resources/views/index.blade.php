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

    @if(session('purchase_success'))
        <div class="alert alert-success alert-dismissible fade show m-3 text-center py-4" role="alert">
            <i class="bi bi-check-circle-fill display-4 d-block mb-3"></i>
            <h4 class="alert-heading">¡Felicidades!</h4>
            <p>{{ session('purchase_success') }}</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @push('scripts')
            <script>
            $(document).ready(function() {
                const clearCart = () => {
                    if (window.Carrito && typeof window.Carrito.vaciar === 'function') {
                        window.Carrito.vaciar();
                        console.log('Carrito vaciado mediante módulo');
                    } else {
                        // Fallback: Borrar directamente del localStorage
                        localStorage.removeItem('carrito_compras_v1');
                        console.log('Carrito vaciado vía localStorage (fallback)');
                    }
                };

                // Ejecutar inmediatamente y con un pequeño retardo por si acaso
                clearCart();
                setTimeout(clearCart, 500);
            });
        </script>
        @endpush
    @endif

    {{-- Carrusel (Estructura Legacy) --}}
    <div id="ContenedorCarrusel" class="carousel slide" data-bs-ride="carousel" aria-label="Carrusel de promociones">
        <div class="carousel-inner">
            @php
                $categorias_destacadas = \App\Models\Categoria::with('productos')->limit(3)->get();
            @endphp
            @foreach($categorias_destacadas as $index => $cat)
                @if($cat->productos->first())
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                        <img src="{{ $cat->productos->first()->imagen_url }}" class="d-block w-100" alt="{{ $cat->nombre }}">
                        <div class="carousel-caption d-none d-md-block">
                            {{-- Caption vacío intencionalmente como en el legacy --}}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#ContenedorCarrusel" data-bs-slide="prev"
            aria-label="Anterior">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#ContenedorCarrusel" data-bs-slide="next"
            aria-label="Siguiente">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    {{-- Productos Favoritos (Estructura Legacy) --}}
    <div id="ProductosFavoritos" class="row container mx-auto mt-5">
        
    </div>

    {{-- Sección Productos Destacados --}}
    <div id="Producto-Catalogo" class="container">
        <h2 class="Pagina mt-5 mb-3 text-center p-2">Productos destacados</h2>
        <div class="row">
            @php
                $productos_destacados = \App\Models\Producto::inRandomOrder()->limit(8)->get();
            @endphp

            @foreach($productos_destacados as $producto)
                <div class="col-12 col-sm-6 col-md-3 mb-4 text-center">
                    <div class="card shadow-sm h-100">
                        <a href="{{ route('productos.show', ['id' => $producto->id]) }}" class="text-decoration-none text-reset">
                            <img class="Imagen-producto img-fluid card-img-top"
                                style="max-height: 250px; height: 200px; object-fit: cover;" src="{{ $producto->imagen_url }}"
                                alt="{{ $producto->nombre }}">
                        </a>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <a href="{{ route('productos.show', ['id' => $producto->id]) }}" class="text-decoration-none text-reset">
                                <h5 class="card-title Titulo-producto mt-2 px-0">{{ $producto->nombre }}</h5>
                                <p class="card-text small text-muted">{{ Str::limit($producto->descripcion, 60) }}</p>
                                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                    <p class="fw-bold text-primary mb-0">${{ number_format($producto->precio, 2) }}</p>
                                    <div class="input-group input-group-sm" style="width: 100px;">
                                        <button class="btn btn-outline-secondary btn-minus" type="button">−</button>
                                        <input type="number" class="form-control text-center qty-input" value="1" min="1"
                                            max="{{ $producto->stock ?? 99 }}">
                                        <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                    </div>
                                </div>
                            </a>
                            <button class="btn btn-sm btn-outline-primary w-100 btn-agregar-carrito mt-auto"
                                data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}" data-precio="{{ $producto->precio }}"
                                data-imagen="{{ $producto->imagen_url }}">
                                Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
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

        // Manejo de cantidad (+/-) y agregar al carrito
        $(document).ready(function () {
            // Botones +/-
            $('.btn-plus').click(function (e) {
                e.preventDefault();
                const input = $(this).siblings('.qty-input');
                const val = parseInt(input.val());
                const max = parseInt(input.attr('max')) || 99;
                if (val < max) input.val(val + 1);
            });

            $('.btn-minus').click(function (e) {
                e.preventDefault();
                const input = $(this).siblings('.qty-input');
                const val = parseInt(input.val());
                if (val > 1) input.val(val - 1);
            });

            $('.btn-agregar-carrito').click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = $(this);
                const card = btn.closest('.card');
                const cantidad = parseInt(card.find('.qty-input').val()) || 1;

                const producto = {
                    id: String(btn.data('id')),
                    nombre: btn.data('nombre'),
                    precio: parseFloat(btn.data('precio')),
                    cantidad: cantidad,
                    imagen: btn.data('imagen')
                };

                if (window.Carrito) {
                    window.Carrito.agregar(producto);

                    // Feedback visual temporal
                    const originalText = btn.text();
                    btn.removeClass('btn-outline-primary').addClass('btn-success').text('¡Agregado!');
                    setTimeout(() => {
                        btn.removeClass('btn-success').addClass('btn-outline-primary').text(originalText);
                    }, 1500);

                } else {
                    console.error('El módulo Carrito no está cargado');
                }
            });
        });
    </script>
@endpush