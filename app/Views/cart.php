<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - MPD</title>
    <link rel="stylesheet" href="<?= base_url('styles/cart.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">
</head>

<body>
    <div class="cart-container">
        <h1 class="cart-title">Mi carrito</h1>
        <div class="cart-layout">
            <!-- LISTA DE ITEMS -->
            <div class="cart-items">
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item <?= ($item['seleccionado'] ?? 0) ? 'selected' : '' ?>"
                    data-price="<?= $item['precio'] ?>"
                    data-libro-id="<?= $item['libro_id'] ?>">

                    <div class="item-image">
                    <img src="<?= base_url($item['foto1'] ?? 'imgs/noimageavailable.jpg') ?>"
                        alt="<?= esc($item['titulo']) ?>">
                    </div>

                    <div class="item-details">
                    <h2 class="item-title"><?= htmlspecialchars($item['titulo']) ?></h2>
                    <p class="item-price-unit">$<?= number_format($item['precio'], 2) ?></p>

                    <div class="item-controls">
                        <div class="quantity-control">
                        <button type="button" class="btn-quantity"
                                data-libro-id="<?= $item['libro_id'] ?>"
                                data-action="minus">-</button>

                        <input type="number" class="quantity-input"
                                value="<?= $item['cantidad'] ?>" min="1" readonly>

                        <button type="button" class="btn-quantity"
                                data-libro-id="<?= $item['libro_id'] ?>"
                                data-action="plus">+</button>
                        </div>

                        <div>
                        <p>Subtotal:</p>
                        <p class="item-price-total">
                            $<?= number_format($item['precio'] * ($item['cantidad'] ?? 1), 2) ?>
                        </p>
                        </div>
                    </div>

                    <div class="item-actions">
                        <button class="btn-select <?= ($item['seleccionado'] ?? 0) ? 'selected' : '' ?>">
                        <?= ($item['seleccionado'] ?? 0) ? 'Deseleccionar' : 'Seleccionar' ?>
                        </button>
                        <button class="btn-remove">Eliminar</button>
                    </div>
                    </div> <!-- .item-details -->

                </div> <!-- .cart-item -->
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>No hay productos en el carrito</p>
                </div>
            <?php endif; ?>
            </div> <!-- FIN .cart-items -->

                        <!-- RESUMEN -->
                        <div class="cart-summary">
                            <div class="summary-card">
                                <h3>Resumen de compra</h3>
                                <div class="summary-row">
                                    <span>Libros seleccionados:</span>
                                    <span id="selected-count">0</span>
                                </div>
                                <div class="summary-row">
                                    <span>Total:</span>
                                    <span id="summary-total">$0.00</span>
                                </div>

                                <!-- Métodos de pago -->
                            <div class="payment-methods">
                                <h4>Método de pago</h4>
                                <div class="payment-option">
                                    <input type="radio" id="efectivo" name="payment_method" value="efectivo" class="ui-radio" required>
                                    <label for="efectivo">En efectivo (presencial)</label>
                                </div>
                                <div class="payment-option">
                                    <input type="radio" id="transferencia" name="payment_method" value="transferencia" class="ui-radio" required>
                                    <label for="transferencia">Transferencia</label>

                                    <!-- Tooltip de Mercado Pago (inicialmente oculto) -->
                                    <div id="mercadopago-tooltip" class="mercadopago-tooltip">
                                        <div class="tooltip-content">
                                            <div class="mp-icon">
                                                <svg viewBox="0 0 24 24" width="20" height="20">
                                                    <path fill="#00a1e1" d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm-1.066 17.28c-2.36 0-3.72-1.65-3.72-4.05v-.48c0-2.34 1.36-4.05 3.72-4.05 1.92 0 3.18 1.26 3.18 3.12v.66H9.894v.78c0 1.38.72 2.22 1.98 2.22.84 0 1.5-.36 1.86-1.02l1.32.78c-.66 1.14-1.86 1.86-3.24 1.86zm7.212 0c-2.16 0-3.54-1.44-3.54-3.6v-.48c0-2.16 1.38-3.6 3.54-3.6s3.54 1.44 3.54 3.6v.48c0 2.16-1.38 3.6-3.54 3.6zm0-1.56c1.08 0 1.74-.84 1.74-2.04v-.48c0-1.2-.66-2.04-1.74-2.04s-1.74.84-1.74 2.04v.48c0 1.2.66 2.04 1.74 2.04z" />
                                                </svg>
                                            </div>
                                            <div class="tooltip-info">
                                                <div class="tooltip-alias">Alias: <strong>mpd.rio3</strong></div>
                                                <div class="tooltip-titular">Titular: <strong>Hernán Mangold</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                <form method="post" action="<?= site_url('cart/checkout') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-buy">Comprar</button>
                                </form>
                            </div>
                        </div>
            </div>
        </div>

<script>
    // Actualizar cantidad (+/-)
    function addToCart(libro, change = 1) {
        fetch('<?= site_url('cart/add/') ?>' + libro, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `change=${change}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + (data.error || 'No se pudo actualizar el carrito.'));
            })
            .catch(() => alert('Error de conexión.'));
    }

    // Función para actualizar el resumen
    function updateSummary() {
        let total = 0;
        let count = 0;
        
        document.querySelectorAll('.cart-item').forEach(item => {
            if (item.classList.contains('selected')) {
                const price = parseFloat(item.dataset.price) || 0;
                const qty = parseInt(item.querySelector('.quantity-input').value) || 1;
                total += price * qty;
                count += qty;
            }
        });
        
        document.getElementById('selected-count').textContent = count;
        document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
        
        // Actualizar estado del botón comprar
        const buyBtn = document.querySelector('.btn-buy');
        if (buyBtn) {
            buyBtn.disabled = count === 0;
        }
    }

    // Evento principal cuando carga la página
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar resumen
        updateSummary();

                // Manejar botones de cantidad - VERSIÓN RÁPIDA
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-quantity');
            if (!btn) return;
            
            const libroId = btn.dataset.libroId;
            const action = btn.dataset.action;
            const item = btn.closest('.cart-item');
            const input = item.querySelector('.quantity-input');
            const subtotalEl = item.querySelector('.item-price-total');
            const price = parseFloat(item.dataset.price);
            
            if (!libroId || !action) return;
            
            // Calcular nueva cantidad
            let newQty = parseInt(input.value);
            if (action === 'plus') {
                newQty++;
            } else if (action === 'minus' && newQty > 1) {
                newQty--;
            } else {
                return; // No hacer nada si es menor a 1
            }
            
            // Cambio visual inmediato (UI optimista)
            input.value = newQty;
            const newSubtotal = price * newQty;
            subtotalEl.textContent = '$' + newSubtotal.toFixed(2);
            updateSummary();
            
            // Actualizar en base de datos
            try {
                const response = await fetch('<?= base_url('cart/update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `libro_id=${libroId}&action=cantidad&value=${newQty}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    // Si falla, revertir cambios visuales
                    const currentQty = parseInt(input.value);
                    const revertQty = action === 'plus' ? currentQty - 1 : currentQty + 1;
                    input.value = revertQty;
                    subtotalEl.textContent = '$' + (price * revertQty).toFixed(2);
                    updateSummary();
                    alert('Error al actualizar cantidad');
                }
            } catch (error) {
                // Si hay error de conexión, revertir cambios
                const currentQty = parseInt(input.value);
                const revertQty = action === 'plus' ? currentQty - 1 : currentQty + 1;
                input.value = revertQty;
                subtotalEl.textContent = '$' + (price * revertQty).toFixed(2);
                updateSummary();
                alert('Error de conexión');
            }
        });

        // Manejar botón eliminar
        document.addEventListener('click', e => {
            const btn = e.target.closest('.btn-remove');
            if (!btn) return;
            
            const item = btn.closest('.cart-item');
            const libroId = item.dataset.libroId;

            if (!confirm('¿Querés borrar este producto del carrito?')) return;

            fetch('<?= base_url('cart/delete') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `libro_id=${libroId}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(res => res.json())
                .then(json => {
                    if (json.success) {
                        item.remove();
                        updateSummary();
                        
                        // Si no quedan items, mostrar carrito vacío
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            document.querySelector('.cart-items').innerHTML = `
                                <div class="empty-cart">
                                    <i class="ti ti-shopping-cart"></i>
                                    <p>No hay productos en el carrito</p>
                                </div>
                            `;
                        }
                    } else {
                        alert(json.error || 'No se pudo borrar el item');
                    }
                })
                .catch(() => alert('Error de red.'));
        });

        // MANEJADOR CORREGIDO para el botón seleccionar/deseleccionar
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-select');
            if (!btn) return;

            e.preventDefault();
            
            const item = btn.closest('.cart-item');
            const libroId = item.dataset.libroId;
            
            // Estado actual y nuevo estado
            const isCurrentlySelected = item.classList.contains('selected');
            const newState = isCurrentlySelected ? 0 : 1;

            // Cambio visual inmediato
            item.classList.toggle('selected');
            btn.classList.toggle('selected');
            btn.textContent = isCurrentlySelected ? 'Seleccionar' : 'Deseleccionar';
            
            // Actualizar resumen
            updateSummary();

            // Actualizar en base de datos
            try {
                const response = await fetch('<?= base_url('cart/update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `libro_id=${libroId}&action=seleccionado&value=${newState}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                });

                const data = await response.json();
                
                if (!data.success) {
                    // Si falla, revertir cambios visuales
                    item.classList.toggle('selected');
                    btn.classList.toggle('selected');
                    btn.textContent = isCurrentlySelected ? 'Deseleccionar' : 'Seleccionar';
                    updateSummary();
                    alert('Error al actualizar selección: ' + (data.error || 'Error desconocido'));
                }
            } catch (error) {
                // Si hay error de conexión, revertir cambios visuales
                item.classList.toggle('selected');
                btn.classList.toggle('selected');
                btn.textContent = isCurrentlySelected ? 'Deseleccionar' : 'Seleccionar';
                updateSummary();
                alert('Error de conexión');
            }
        });
    });
</script>
</body>

</html>