window.Carrito = (function () {
    let items = [];

    let $badge = null;
    let $contenedorItems = null;
    let $total = null;

    function isAuth() {
        const meta = document.querySelector('meta[name="is-auth"]');
        return meta && meta.getAttribute('content') === '1';
    }

    function csrf() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function init() {
        $badge = document.getElementById('carrito-badge');
        $contenedorItems = document.getElementById('carrito-items');
        $total = document.getElementById('carrito-total');

        if (isAuth()) {
            cargarItems(false);
        } else {
            items = [];
            actualizarBadge();
            render(0);
        }

        const modal = document.getElementById('modalCarrito');
        if (modal) {
            modal.addEventListener('show.bs.modal', () => {
                if (isAuth()) cargarItems(false);
                else render(0);
            });
        }

        if ($contenedorItems) {
            $contenedorItems.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn-eliminar-item');
                if (!btn) return;
                eliminar(btn.dataset.id);
            });
        }

        const btnVaciar = document.getElementById('btn-vaciar-carrito');
        if (btnVaciar) {
            btnVaciar.addEventListener('click', (e) => {
                e.preventDefault();
                vaciar();
            });
        }

        // ✅ Delegación global: cards + detalle (modalCompra)
        document.addEventListener('click', function (e) {
            const btnCar = e.target.closest('.btn-agregar-carrito');
            const btnConfirmar = e.target.closest('#btn-confirmar'); // botón del modal en detalle

            const btn = btnCar || btnConfirmar;
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            if (!window.Carrito || typeof window.Carrito.agregar !== 'function') {
                alert('No se pudo agregar el producto. Inténtalo de nuevo.');
                return;
            }

            let cantidad = 1;
            let id = '';
            let nombre = '';
            let precio = 0;
            let imagen = '';

            if (btnConfirmar) {
                // ✅ detalle: tomar select + cantidad
                const sel = document.getElementById('selectProducto');
                const qty = document.getElementById('cantidad');

                id = sel ? String(sel.value || '') : '';
                nombre = sel ? String(sel.options[sel.selectedIndex]?.text || '').trim() : '';
                cantidad = qty ? (parseInt(qty.value, 10) || 1) : 1;

                // en detalle, precio/imagen del producto actual (y tú rediriges al cambiar select)
                precio = parseFloat(btn.getAttribute('data-precio') || btn.dataset.precio || '0');
                imagen = btn.getAttribute('data-imagen') || btn.dataset.imagen || '';

            } else {
                // ✅ cards: tomar dataset + qty-input si existe
                const card = btn.closest('.card') || document;
                const qtyInput = card.querySelector('.qty-input') || document.querySelector('.qty-input');

                if (qtyInput) {
                    const v = parseInt(qtyInput.value, 10);
                    if (!isNaN(v) && v > 0) cantidad = v;
                }

                id = String(btn.dataset.id || btn.getAttribute('data-id') || '');
                nombre = btn.dataset.nombre || btn.getAttribute('data-nombre') || '';
                precio = parseFloat(btn.dataset.precio || btn.getAttribute('data-precio') || '0');
                imagen = btn.dataset.imagen || btn.getAttribute('data-imagen') || '';
            }

            if (!id) {
                alert('No se pudo agregar el producto. Inténtalo de nuevo.');
                return;
            }

            const producto = { id, nombre, precio, cantidad, imagen };
            window.Carrito.agregar(producto);
        }, true);
    }

    async function api(url, method = 'GET', data = null, redirectOn401 = true) {
        const opts = {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
            credentials: 'same-origin',
        };

        if (data) opts.body = JSON.stringify(data);

        const res = await fetch(url, opts);

        if (res.status === 401) {
            if (redirectOn401) {
                alert('Debes iniciar sesión para usar el carrito.');
                window.location.href = '/login';
            }
            throw new Error('401');
        }

        if (res.status === 419) {
            alert('Tu sesión expiró. Recarga la página e intenta de nuevo.');
            throw new Error('419');
        }

        if (!res.ok) {
            let msg = 'Ocurrió un error.';
            try {
                const j = await res.json();
                msg = j.error || j.message || msg;
            } catch (_) {
                try {
                    const t = await res.text();
                    if (t) msg = t;
                } catch (_) { }
            }
            throw new Error(msg);
        }

        return res.json();
    }

    function cargarItems(redirectOn401 = false) {
        api('/carrito/items', 'GET', null, redirectOn401)
            .then((data) => {
                items = data.items || [];
                actualizarBadge();
                render(data.total || 0);
            })
            .catch((e) => {
                console.error('carrito/items:', e.message || e);
            });
    }

    function actualizarBadge() {
        if (!$badge) return;
        const totalItems = items.reduce((acc, item) => acc + (parseInt(item.cantidad, 10) || 0), 0);
        $badge.textContent = totalItems;
        $badge.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }

    function agregar(producto) {
        if (!producto || !producto.id) return;

        const cantidad = parseInt(producto.cantidad, 10) || 1;

        api('/carrito/agregar', 'POST', {
            pro_codigo: String(producto.id),
            cantidad: cantidad
        }, true)
            .then(() => cargarItems(false))
            .catch((e) => {
                console.error('carrito/agregar:', e.message || e);
                if (e.message && e.message !== '401' && e.message !== '419') {
                    alert('No se pudo agregar al carrito. Inténtalo de nuevo.');
                }
            });
    }

    function eliminar(id) {
        api('/carrito/eliminar', 'POST', { pro_codigo: String(id) }, true)
            .then(() => cargarItems(false))
            .catch((e) => {
                console.error('carrito/eliminar:', e.message || e);
                alert('No se pudo eliminar el producto. Inténtalo de nuevo.');
            });
    }

    function vaciar() {
        api('/carrito/vaciar', 'POST', {}, true)
            .then(() => cargarItems(false))
            .catch((e) => {
                console.error('carrito/vaciar:', e.message || e);
                alert('No se pudo vaciar el carrito. Inténtalo de nuevo.');
            });
    }

    function render(totalBackend) {
        if (!$contenedorItems || !$total) return;

        $contenedorItems.innerHTML = '';

        if (!items || items.length === 0) {
            $contenedorItems.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-cart-x display-1 text-muted"></i>
                    <p class="mt-3 text-muted">Tu carrito está vacío</p>
                </div>
            `;
            $total.textContent = '0.00';
            return;
        }

        const lista = document.createElement('ul');
        lista.className = 'list-group list-group-flush';

        items.forEach((item) => {
            const precio = Number(item.precio) || 0;
            const cantidad = Number(item.cantidad) || 0;
            const subtotal = precio * cantidad;

            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';

            li.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.imagen || ''}" alt="${item.alt_imagen || item.nombre || 'Producto'}"
                        style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <div>
                        <h6 class="mb-0">${item.nombre || ''}</h6>
                        <small class="text-muted">$${precio.toFixed(2)} x ${cantidad}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold">$${subtotal.toFixed(2)}</span>
                    <button class="btn btn-sm btn-outline-danger btn-eliminar-item"
                        data-id="${item.id}" aria-label="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            lista.appendChild(li);
        });

        $contenedorItems.appendChild(lista);
        $total.textContent = Number(totalBackend || 0).toFixed(2);
    }

    return { init, agregar, eliminar, vaciar, cargarItems };
})();

document.addEventListener('DOMContentLoaded', () => {
    if (window.Carrito) window.Carrito.init();
});
