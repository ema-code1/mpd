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
        <h1 class="cart-title">Mi Carrito de Compras</h1>

        <div class="cart-layout">
            <!-- Lista de Libros en el Carrito -->
            <div class="cart-items">
                <?php if (!empty($cartItems)): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" data-id="<?= $item['id'] ?>">
                            <div class="item-image">
                                <img src="<?= base_url($item['foto1'] ?? 'imgs/noimageavailable.jpg') ?>" alt="<?= esc($item['titulo']) ?>">
                            </div>
                            <div class="item-details">
                                <h3 class="item-title"><?= esc($item['titulo']) ?></h3>
                                <p class="item-price-unit">Precio unitario: $<span class="price-unit"><?= number_format($item['precio'], 2) ?></span></p>
                                <div class="item-controls">
                                    <div class="quantity-control">
                                        <button type="button" class="btn-quantity" onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                                        <input type="number" class="quantity-input" value="<?= $item['cantidad'] ?>" min="1" data-id="<?= $item['id'] ?>" onchange="updateQuantity(<?= $item['id'] ?>, 0, this.value)">
                                        <button type="button" class="btn-quantity" onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="btn-select" onclick="toggleSelect(<?= $item['id'] ?>, <?= $item['seleccionado'] ?>)">
                                        <i class="ti ti-<?= $item['seleccionado'] ? 'checkbox' : 'square' ?>"></i>
                                        <span><?= $item['seleccionado'] ? 'Seleccionado' : 'Seleccionar para comprar' ?></span>
                                    </button>
                                    <button type="button" class="btn-remove" onclick="removeFromCart(<?= $item['id'] ?>)">
                                        <i class="ti ti-trash"></i>
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-cart">
                        <i class="ti ti-shopping-cart-off"></i>
                        <p>Tu carrito está vacío.</p>
                        <a href="<?= site_url('/') ?>" class="btn-back">Ir al catálogo</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Panel de Resumen -->
            <div class="cart-summary">
                <div class="summary-card">
                    <h3>Resumen de Compra</h3>
                    <div class="summary-row">
                        <span>Libros seleccionados:</span>
                        <strong id="selected-count">0</strong>
                    </div>
                    <div class="summary-row">
                        <span>Total a pagar:</span>
                        <strong id="total-price">$0.00</strong>
                    </div>
                    <button type="button" class="btn-buy" id="checkout-btn" disabled>
                        <i class="ti ti-shopping-cart"></i>
                        Proceder al Pago
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?= view('templates/footer') ?>

    <script>
        // Función para actualizar cantidad
        function updateQuantity(itemId, delta, newValue = null) {
            const input = document.querySelector(`.cart-item[data-id="${itemId}"] .quantity-input`);
            let newQty = newValue !== null ? parseInt(newValue) : parseInt(input.value) + delta;
            if (newQty < 1) newQty = 1;

            fetch('<?= site_url('cart/update') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${itemId}&action=cantidad&value=${newQty}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    input.value = newQty;
                    const unitPrice = parseFloat(document.querySelector(`.cart-item[data-id="${itemId}"] .price-unit`).textContent);
                    const totalSpan = document.querySelector(`.cart-item[data-id="${itemId}"] .price-total`);
                    totalSpan.textContent = (unitPrice * newQty).toFixed(2);
                    updateSummary();
                }
            });
        }

        // Función para alternar selección
        function toggleSelect(itemId, currentStatus) {
            const newStatus = currentStatus ? 0 : 1;
            const itemElement = document.querySelector(`.cart-item[data-id="${itemId}"]`);
            const button = itemElement.querySelector('.btn-select');

            fetch('<?= site_url('cart/update') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `item_id=${itemId}&action=seleccionado&value=${newStatus}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (newStatus) {
                        itemElement.classList.add('selected');
                        button.innerHTML = '<i class="ti ti-checkbox"></i><span>Seleccionado</span>';
                    } else {
                        itemElement.classList.remove('selected');
                        button.innerHTML = '<i class="ti ti-square"></i><span>Seleccionar para comprar</span>';
                    }
                    updateSummary();
                }
            });
        }

        // Función para eliminar item
        function removeFromCart(itemId) {
            if (confirm('¿Eliminar este libro del carrito?')) {
                fetch('<?= site_url('cart/remove/') ?>' + itemId, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`.cart-item[data-id="${itemId}"]`).remove();
                        updateSummary();
                    }
                });
            }
        }

        // Función para actualizar el resumen (total y cantidad)
        function updateSummary() {
            let total = 0;
            let count = 0;
            document.querySelectorAll('.cart-item.selected').forEach(item => {
                const price = parseFloat(item.querySelector('.price-total').textContent);
                total += price;
                count++;
            });

            document.getElementById('selected-count').textContent = count;
            document.getElementById('total-price').textContent = '$' + total.toFixed(2);

            const checkoutBtn = document.getElementById('checkout-btn');
            checkoutBtn.disabled = count === 0;
        }

        // Inicializar resumen al cargar la página
        document.addEventListener('DOMContentLoaded', updateSummary);
    </script>
</body>
</html>