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
                                        <button type="button"
                                            class="btn-quantity"
                                            data-libro-id="<?= $item['libro_id'] ?>"
                                            data-action="minus">-</button>

                                        <input type="number" class="quantity-input"
                                            value="<?= $item['cantidad'] ?>" min="1" readonly>

                                        <button type="button"
                                            class="btn-quantity"
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
                                    <button class="btn-select <?= ($item['seleccionado'] ?? 0) ? 'active' : '' ?>">
                                        <?= ($item['seleccionado'] ?? 0) ? 'Deseleccionar' : 'Seleccionar' ?>
                                    </button>
                                    <button class="btn-remove">Eliminar</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <p>No hay productos en el carrito</p>
                        </div>
                    <?php endif; ?>
                        </div>

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
                                        <input type="checkbox" id="efectivo" name="payment_method" value="efectivo" class="ui-checkbox">
                                        <label for="efectivo">En efectivo (presencial)</label>
                                    </div>
                                    <div class="payment-option">
                                        <input type="checkbox" id="transferencia" name="payment_method" value="transferencia" class="ui-checkbox">
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
                                    <button type="submit" class="btn btn-primary">Comprar</button>
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

            // Actualizar selección (toggle entre 0 y 1)
            function updateSelection(libroId, newState) {
                fetch('<?= base_url('cart/update') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `libro_id=${libroId}&action=seleccionado&value=${newState}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            alert('Error al actualizar selección: ' + (data.error || 'Error desconocido'));
                            // Revertir visualmente si hay error
                            location.reload();
                        }
                    })
                    .catch(() => {
                        alert('Error de conexión');
                        location.reload();
                    });
            }

            // Manejador de botones de cantidad
            document.addEventListener('click', e => {
                const btn = e.target.closest('.btn-quantity');
                if (!btn) return;
                const libroId = btn.dataset.libroId;
                const action = btn.dataset.action;
                if (libroId && (action === 'plus' || action === 'minus')) {
                    const change = (action === 'plus') ? 1 : -1;
                    addToCart(parseInt(libroId), change);
                }
            });

            // Control del carrito (selección, borrado, totales)
            document.addEventListener('DOMContentLoaded', () => {
                const container = document.querySelector('.cart-items');
                const buyBtn = document.querySelector('.btn-primary');

                function updateSummary() {
                    let total = 0,
                        count = 0;
                    document.querySelectorAll('.cart-item').forEach(item => {
                        const price = parseFloat(item.dataset.price) || 0;
                        const qty = parseInt(item.querySelector('.quantity-input').value) || 0;
                        const isSelected = item.classList.contains('selected');

                        if (isSelected) {
                            total += price * qty;
                            count += qty;
                        }

                        const subtotal = item.querySelector('.item-price-total');
                        if (subtotal) subtotal.textContent = '$' + (price * qty).toFixed(2);
                    });

                    document.getElementById('selected-count').textContent = count;
                    document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
                    if (buyBtn) {
                        buyBtn.disabled = count === 0;
                    }
                }

                // Eventos de selección y eliminación
                container.addEventListener('click', e => {
                    const btn = e.target.closest('button');
                    if (!btn) return;
                    const item = btn.closest('.cart-item');
                    const libroId = item.dataset.libroId;

                    // Seleccionar/Deseleccionar
                    if (btn.classList.contains('btn-select')) {
                        const isCurrentlySelected = item.classList.contains('selected');
                        const newState = isCurrentlySelected ? 0 : 1;

                        // Cambiar visualmente inmediatamente
                        btn.classList.toggle('active');
                        item.classList.toggle('selected');
                        updateSummary();

                        // Actualizar en base de datos
                        updateSelection(libroId, newState);
                    }

                    // Eliminar
                    if (btn.classList.contains('btn-remove')) {
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
                                    alert('Producto eliminado correctamente');
                                    location.reload();
                                } else {
                                    alert(json.error || 'No se pudo borrar el item');
                                }
                            })
                            .catch(() => alert('Error de red.'));
                    }
                });

                updateSummary();
            });


            // Control de métodos de pago
            document.addEventListener('DOMContentLoaded', function() {
                const efectivoCheckbox = document.getElementById('efectivo');
                const transferenciaCheckbox = document.getElementById('transferencia');
                const mercadopagoTooltip = document.getElementById('mercadopago-tooltip');
                const btnComprar = document.getElementById('btn-comprar');

                // Control de checkboxes exclusivos
                efectivoCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        transferenciaCheckbox.checked = false;
                        mercadopagoTooltip.classList.remove('show');
                    }
                    updateBuyButton();
                });

                transferenciaCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        efectivoCheckbox.checked = false;
                        mercadopagoTooltip.classList.add('show');
                    } else {
                        mercadopagoTooltip.classList.remove('show');
                    }
                    updateBuyButton();
                });

                // Función para actualizar estado del botón comprar
                function updateBuyButton() {
                    const hasPaymentMethod = efectivoCheckbox.checked || transferenciaCheckbox.checked;
                    const hasSelectedItems = parseInt(document.getElementById('selected-count').textContent) > 0;

                    btnComprar.disabled = !(hasPaymentMethod && hasSelectedItems);
                }

                // Event listener para el botón comprar
                btnComprar.addEventListener('click', function(e) {
                    e.preventDefault();

                    if (!efectivoCheckbox.checked && !transferenciaCheckbox.checked) {
                        showToast('Por favor, selecciona un método de pago');
                        return;
                    }

                    // Obtener método de pago seleccionado
                    const paymentMethod = efectivoCheckbox.checked ? 'efectivo' : 'transferencia';

                    // Guardar en pre_venta (solo si es efectivo)
                    if (paymentMethod === 'efectivo') {
                        savePreVenta();
                    }

                    // Aquí continuar con el proceso de compra normal
                    processPurchase(paymentMethod);
                });

                // Función para mostrar toast
                function showToast(message) {
                    // Crear toast si no existe
                    let toast = document.getElementById('payment-toast');
                    if (!toast) {
                        toast = document.createElement('div');
                        toast.id = 'payment-toast';
                        toast.className = 'toast';
                        toast.innerHTML = `
                <div class="toast-content">
                    <span class="toast-icon">⚠️</span>
                    <span class="toast-message">${message}</span>
                </div>
            `;
                        document.body.appendChild(toast);
                    } else {
                        toast.querySelector('.toast-message').textContent = message;
                    }

                    // Mostrar toast
                    toast.classList.add('show');

                    // Ocultar después de 4 segundos
                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 4000);
                }

                // Función para guardar en pre_venta
                function savePreVenta() {
                    // Aquí va tu código AJAX para guardar en la tabla pre_venta
                    // Solo los elementos del carrito que están seleccionados
                    fetch('guardar_preventa.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                payment_method: 'efectivo',
                                // otros datos necesarios
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Pre-venta guardada:', data);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }

                // Función para procesar la compra
                function processPurchase(paymentMethod) {
                    // Tu código existente para procesar la compra
                    console.log('Procesando compra con método:', paymentMethod);
                }
            });
        </script>
</body>

</html>