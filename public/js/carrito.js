/**
 * Módulo de Carrito de Compras
 * Maneja la lógica del carrito usando localStorage
 */
window.Carrito = (function () {
    // Clave para localStorage
    const STORAGE_KEY = 'carrito_compras_v1';

    // Estado interno
    let items = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];

    // Selectores DOM (se inicializan cuando el DOM está listo)
    let $badge = null;
    let $contenedorItems = null;
    let $total = null;

    /**
     * Inicializar selectores y renderizar estado inicial
     */
    function init() {
        $badge = document.getElementById('carrito-badge');
        $contenedorItems = document.getElementById('carrito-items');
        $total = document.getElementById('carrito-total');

        actualizarBadge();

        // Escuchar eventos de eliminación delegados
        if ($contenedorItems) {
            $contenedorItems.addEventListener('click', (e) => {
                if (e.target.closest('.btn-eliminar-item')) {
                    const id = parseInt(e.target.closest('.btn-eliminar-item').dataset.id);
                    eliminar(id);
                }
            });
        }

        // Renderizar si el modal se abre
        const modalCarrito = document.getElementById('modalCarrito');
        if (modalCarrito) {
            modalCarrito.addEventListener('show.bs.modal', render);
        }
    }

    /**
     * Guardar en localStorage
     */
    function guardar() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        actualizarBadge();
    }

    /**
     * Actualizar contador visual
     */
    function actualizarBadge() {
        if (!$badge) return;

        const totalItems = items.reduce((acc, item) => acc + item.cantidad, 0);
        $badge.textContent = totalItems;

        if (totalItems > 0) {
            $badge.style.display = 'block';
        } else {
            $badge.style.display = 'none';
        }
    }

    /**
     * Agregar producto al carrito
     */
    function agregar(producto) {
        const itemExistente = items.find(i => i.id === producto.id);

        if (itemExistente) {
            itemExistente.cantidad += producto.cantidad;
        } else {
            items.push({
                id: producto.id,
                nombre: producto.nombre,
                precio: producto.precio,
                cantidad: producto.cantidad,
                imagen: producto.imagen
            });
        }

        guardar();
        // Opcional: Renderizar si el modal está abierto (aunque usualmente se agrega desde detalle)
    }

    /**
     * Eliminar producto del carrito
     */
    function eliminar(id) {
        items = items.filter(i => i.id !== id);
        guardar();
        render(); // Re-renderizar para mostrar cambios visuales inmediatamente
    }

    /**
     * Calcular total
     */
    function calcularTotal() {
        return items.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
    }

    /**
     * Renderizar items en el modal
     */
    function render() {
        if (!$contenedorItems || !$total) return;

        $contenedorItems.innerHTML = '';

        if (items.length === 0) {
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

        items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center px-0';

            li.innerHTML = `
                <div class="d-flex align-items-center gap-3">
                    <img src="${item.imagen}" alt="${item.nombre}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    <div>
                        <h6 class="mb-0">${item.nombre}</h6>
                        <small class="text-muted">$${item.precio.toFixed(2)} x ${item.cantidad}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="fw-bold">$${(item.precio * item.cantidad).toFixed(2)}</span>
                    <button class="btn btn-sm btn-outline-danger btn-eliminar-item" data-id="${item.id}" aria-label="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

            lista.appendChild(li);
        });

        $contenedorItems.appendChild(lista);
        $total.textContent = calcularTotal().toFixed(2);
    }

    /**
     * Vaciar todo el carrito
     */
    function vaciar() {
        items = [];
        guardar();
        render();
    }

    // API Pública
    return {
        init,
        agregar,
        eliminar,
        vaciar,
        getItems: () => [...items]
    };
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.Carrito.init();
});
