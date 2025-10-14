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
                        <div class="cart-item"
                             data-price="<?= $item['precio'] ?>"
                             data-libro-id="<?= $item['libro_id'] ?>"> <!-- 👈 CAMBIO AQUÍ -->

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
                                        Seleccionar
                                    </button>
                                    <button class="btn-remove">Eliminar</button>
                                </div>
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
                    <button class="btn-buy" disabled>Comprar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Actualizar cantidad (+/-)
        function addToCart(libro, change = 1) {
            fetch('<?= site_url('cart/add/') ?>' + libro, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `change=${change}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert('Error: ' + (data.error || 'No se pudo actualizar el carrito.'));
            })
            .catch(() => alert('Error de conexión.'));
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
            const buyBtn = document.querySelector('.btn-buy');

            function updateSummary() {
                let total = 0, count = 0;
                document.querySelectorAll('.cart-item').forEach(item => {
                    const price = parseFloat(item.dataset.price) || 0;
                    const qty = parseInt(item.querySelector('.quantity-input').value) || 0;

                    if (item.classList.contains('selected')) {
                        total += price * qty;
                        count += qty;
                    }

                    const subtotal = item.querySelector('.item-price-total');
                    if (subtotal) subtotal.textContent = '$' + (price * qty).toFixed(2);
                });

                document.getElementById('selected-count').textContent = count;
                document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
                buyBtn.disabled = count === 0;
            }

            // Eventos de selección y eliminación
            container.addEventListener('click', e => {
                const btn = e.target.closest('button');
                if (!btn) return;
                const item = btn.closest('.cart-item');
                const libroId = item.dataset.libroId;

                // Seleccionar
                if (btn.classList.contains('btn-select')) {
                    btn.classList.toggle('active');
                    item.classList.toggle('selected');
                    updateSummary();
                }

                // Eliminar
                if (btn.classList.contains('btn-remove')) {
                    if (!confirm('¿Querés borrar este producto del carrito?')) return;
                    fetch('<?= base_url('cart/delete') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'libro_id=' + libroId // 👈 CAMBIO CLAVE
                    })
                    .then(res => res.json())
                    .then(json => {
                        if (json.success) {
                            item.remove();
                            updateSummary();
                            alert('Producto eliminado correctamente');
                        } else {
                            alert(json.error || 'No se pudo borrar el item');
                        }
                    })
                    .catch(() => alert('Error de red.'));
                }
            });

            updateSummary();
        });
    </script>
</body>
</html>
