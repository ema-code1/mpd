<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - MPD</title>
    <link rel="stylesheet" href="<?= base_url('styles/index.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">
</head>
<body>
    <?= view('templates/header') ?>

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
                                    <p class="item-price-total">Subtotal: $<span class="price-total"><?= number_format($item['precio'] * $item['cantidad'], 2) ?></span></p>
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

    <style>
        .cart-container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 20px;
        }

        .cart-title {
            text-align: center;
            font-size: 2rem;
            color: var(--text);
            margin-bottom: 2rem;
            font-weight: 700;
        }

        .cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .cart-items {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .cart-item {
            display: flex;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
        }

        .cart-item:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        .cart-item.selected {
            border: 2px solid var(--primary);
            background-color: #fff9f0;
        }

        .item-image img {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            margin-right: 1.5rem;
        }

        .item-details {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-title {
            font-size: 1.5rem;
            margin: 0 0 0.5rem 0;
            color: var(--text);
        }

        .item-price-unit {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin: 0 0 1rem 0;
        }

        .item-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-quantity {
            width: 30px;
            height: 30px;
            border: 1px solid var(--border);
            background: var(--white);
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
            transition: var(--transition);
        }

        .btn-quantity:hover {
            background: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            padding: 5px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
        }

        .item-price-total {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
            margin: 0;
        }

        .item-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-select, .btn-remove {
            padding: 8px 16px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }

        .btn-select {
            background: var(--background);
            color: var(--text);
        }

        .btn-select:hover {
            background: var(--primary);
            color: var(--white);
        }

        .btn-remove {
            background: #ffe0e0;
            color: #d32f2f;
        }

        .btn-remove:hover {
            background: #d32f2f;
            color: var(--white);
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--background);
            border-radius: var(--radius-lg);
        }

        .empty-cart i {
            font-size: 4rem;
            color: var(--text-tertiary);
            margin-bottom: 1rem;
        }

        .cart-summary {
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .summary-card {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .btn-buy {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            font-weight: 600;
            border: none;
            border-radius: var(--radius-lg);
            cursor: pointer;
            margin-top: 1.5rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-buy:hover:not(:disabled) {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--hover) 100%);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        .btn-buy:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 968px) {
            .cart-layout {
                grid-template-columns: 1fr;
            }
            .cart-summary {
                position: static;
                margin-top: 2rem;
            }
        }
    </style>
</body>
</html>