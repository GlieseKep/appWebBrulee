@extends('layouts.app')

@section('title', 'Brúlée — Finalizar Compra')

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Finalizar Compra</h1>
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Datos de Facturación y Pago</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('checkout.procesar') }}" method="POST" id="checkout-form">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                        id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                                    @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="cedula" class="form-label">Cédula (10 dígitos)</label>
                                    <input type="text" class="form-control @error('cedula') is-invalid @enderror"
                                        id="cedula" name="cedula" value="{{ old('cedula') }}" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="10" required>
                                    @error('cedula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <label for="direccion" class="form-label">Dirección de Facturación</label>
                                    <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                        id="direccion" name="direccion" value="{{ old('direccion') }}" required>
                                    @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Información de Pago</h5>

                                <div class="col-md-6">
                                    <label for="metodo_pago" class="form-label">Método de Pago</label>
                                    <select class="form-select @error('metodo_pago') is-invalid @enderror" id="metodo_pago"
                                        name="metodo_pago" required>
                                        <option value="TARJETA" selected>Tarjeta de Crédito / Débito</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="numero_tarjeta" class="form-label">Número de Tarjeta</label>
                                    <input type="text" class="form-control @error('numero_tarjeta') is-invalid @enderror"
                                        id="numero_tarjeta" name="numero_tarjeta" maxlength="16" inputmode="numeric"
                                        pattern="[0-9]*" placeholder="0000000000000000" required>
                                    @error('numero_tarjeta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="mes_caducidad" class="form-label">Mes Exp.</label>
                                    <input type="number" class="form-control @error('mes_caducidad') is-invalid @enderror"
                                        id="mes_caducidad" name="mes_caducidad" min="1" max="12" placeholder="MM" required>
                                </div>

                                <div class="col-md-4">
                                    <label for="year_caducidad" class="form-label">Año Exp.</label>
                                    <input type="number" class="form-control @error('year_caducidad') is-invalid @enderror"
                                        id="year_caducidad" name="year_caducidad" min="{{ date('Y') }}" placeholder="YYYY"
                                        required>
                                </div>

                                <div class="col-md-4">
                                    <label for="codigo_seguridad" class="form-label">CVV</label>
                                    <input type="text" class="form-control @error('codigo_seguridad') is-invalid @enderror"
                                        id="codigo_seguridad" name="codigo_seguridad" maxlength="3" inputmode="numeric"
                                        pattern="[0-9]*" placeholder="123" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="acepta_terminos"
                                            name="acepta_terminos" value="S">
                                        <label class="form-check-label" for="acepta_terminos">
                                            <strong>Aceptar términos y condiciones:</strong> Al marcar esta casilla,
                                            autorizo a Brúlée a guardar mis datos de facturación para futuras compras. Si no
                                            acepta, deberá ingresar los datos de su tarjeta en cada nueva compra.
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mt-5">
                                <button type="submit" class="btn btn-primary btn-lg">Pagar Ahora</button>
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Resumen del Pedido</h5>
                    </div>
                    <div class="card-body" id="checkout-summary">
                        {{-- Se llenará con JS desde el carrito --}}
                        <div class="text-center py-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            if (window.Carrito) {
                const items = window.Carrito.getItems();
                if (items.length === 0) {
                    window.location.href = "{{ route('home') }}";
                    return;
                }

                let summaryHtml = '<ul class="list-group list-group-flush mb-3">';
                let total = 0;

                items.forEach(item => {
                    const subtotal = item.precio * item.cantidad;
                    total += subtotal;
                    summaryHtml += `
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <h6 class="my-0">${item.nombre}</h6>
                                        <small class="text-muted">Cant: ${item.cantidad}</small>
                                    </div>
                                    <span class="text-muted">$${subtotal.toFixed(2)}</span>
                                </li>
                            `;
                });

                summaryHtml += `
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <strong>Total (USD)</strong>
                                <strong>$${total.toFixed(2)}</strong>
                            </li>
                        </ul>`;

                $('#checkout-summary').html(summaryHtml);
            }

            // Restringir a solo números en campos específicos
            $('#cedula, #numero_tarjeta, #codigo_seguridad').on('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
@endpush