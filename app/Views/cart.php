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
        
        <!-- Mostrar mensajes de sesión -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" style="padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 10px; margin-bottom: 20px; color: #155724;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-error" style="padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 10px; margin-bottom: 20px; color: #721c24;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

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
                <i class="ti ti-shopping-cart"></i>
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

                    <!-- ===== FORMULARIO DE CHECKOUT ===== -->
<form method="post" action="<?= base_url('api/checkout') ?>" enctype="multipart/form-data" id="checkoutForm">
    <?= csrf_field() ?>

    <!-- Input hidden para el ID de venta -->
    <input type="hidden" name="venta_id" id="ventaId" value="">

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

            <!-- Tooltip de Mercado Pago -->
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

        <!-- Input para archivo de transferencia (oculto por defecto) -->
        <div id="comprobante-section" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <label for="comprobante" style="display: block; margin-bottom: 0.5rem; color: var(--text); font-weight: 600;">
                Adjuntar comprobante de transferencia
            </label>
            <input type="file" id="comprobante" name="comprobante" accept=".jpg,.jpeg,.png,.pdf" style="
                width: 100%;
                padding: 10px;
                border: 2px dashed var(--primary);
                border-radius: var(--radius-sm);
                background: #fff0e4;
                cursor: pointer;
            ">
            <small style="display: block; margin-top: 0.5rem; color: var(--text-secondary);">
                Formatos permitidos: JPG, PNG, PDF (máximo 5MB)
            </small>
            <div id="file-name" style="margin-top: 0.5rem; color: var(--primary); font-weight: 600; display: none;"></div>
        </div>
    </div>

    <button type="submit" class="btn-buy" id="buyBtn" disabled>Comprar</button>
</form>


<!-- ===== JAVASCRIPT ===== -->
<script>
    // ===== VARIABLES GLOBALES =====
    let cartData = {
        items: [],
        selected: new Set()
    };

    // ===== FUNCIÓN: Actualizar cantidad (+/-) =====
    async function addToCart(libro, change = 1) {
        try {
            const response = await fetch('<?= base_url('cart/add/') ?>' + libro, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `change=${change}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            });
            
            const data = await response.json();
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'No se pudo actualizar el carrito.'));
            }
        } catch (error) {
            alert('Error de conexión.');
        }
    }

    // ===== FUNCIÓN: Actualizar el resumen =====
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
        
        updateBuyButton();
    }

    // ===== FUNCIÓN: Validar si se puede comprar =====
    function updateBuyButton() {
        const buyBtn = document.getElementById('buyBtn');
        const selectedCount = parseInt(document.getElementById('selected-count').textContent) || 0;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const transferencia = document.getElementById('transferencia').checked;
        const comprobante = document.getElementById('comprobante');

        let canBuy = selectedCount > 0 && paymentMethod;
        
        if (transferencia && !comprobante.value) {
            canBuy = false;
        }

        buyBtn.disabled = !canBuy;
    }

    // ===== FUNCIÓN: Crear venta y proceder al checkout =====
    async function procederCheckout(e) {
        e.preventDefault();

        const buyBtn = document.getElementById('buyBtn');
        buyBtn.disabled = true;
        buyBtn.textContent = 'Procesando...';

        try {
            // Recopilar items seleccionados
            const items = [];
            document.querySelectorAll('.cart-item.selected').forEach(item => {
                items.push({
                    libro_id: item.dataset.libroId,
                    cantidad: parseInt(item.querySelector('.quantity-input').value),
                    precio: parseFloat(item.dataset.price)
                });
            });

            if (items.length === 0) {
                alert('Selecciona al menos un producto');
                buyBtn.disabled = false;
                buyBtn.textContent = 'Comprar';
                return;
            }

            // Datos del formulario
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const total = parseFloat(document.getElementById('summary-total').textContent.replace('$', ''));
            const comprobante = document.getElementById('comprobante').files[0];

            // Crear FormData para enviar datos y archivo
            const formData = new FormData();
            formData.append('payment_method', paymentMethod);
            formData.append('items', JSON.stringify(items));
            formData.append('total', total);
            if (comprobante) {
                formData.append('comprobante', comprobante);
            }
            formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

            // Enviar al servidor para crear la venta
            const response = await fetch('<?= base_url('api/checkout/crear') ?>', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success && result.venta_id) {
                // Rellenar el input hidden con el ID de venta
                document.getElementById('ventaId').value = result.venta_id;

                // Redirigir al checkout
                window.location.href = '<?= base_url('checkout?id=') ?>' + result.venta_id;
            } else {
                alert('Error: ' + (result.error || 'No se pudo crear la venta'));
                buyBtn.disabled = false;
                buyBtn.textContent = 'Comprar';
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Error de conexión: ' + error.message);
            buyBtn.disabled = false;
            buyBtn.textContent = 'Comprar';
        }
    }

    // ===== INICIALIZACIÓN =====
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar resumen
        updateSummary();

        // ===== Evento: Submit del formulario =====
        document.getElementById('checkoutForm').addEventListener('submit', procederCheckout);

        // ===== Evento: Cambio de método de pago =====
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const comprobanteSection = document.getElementById('comprobante-section');
                const comprobanteInput = document.getElementById('comprobante');
                
                if (this.value === 'transferencia') {
                    comprobanteSection.style.display = 'block';
                    comprobanteInput.required = true;
                } else {
                    comprobanteSection.style.display = 'none';
                    comprobanteInput.required = false;
                    comprobanteInput.value = '';
                    document.getElementById('file-name').style.display = 'none';
                }
                
                updateBuyButton();
            });
        });

        // ===== Evento: Mostrar nombre del archivo =====
        document.getElementById('comprobante').addEventListener('change', function() {
            const fileNameDiv = document.getElementById('file-name');
            if (this.value) {
                fileNameDiv.textContent = '✓ Archivo: ' + this.files[0].name;
                fileNameDiv.style.display = 'block';
            } else {
                fileNameDiv.style.display = 'none';
            }
            updateBuyButton();
        });

        // ===== Evento: Botones de cantidad (+ y -) =====
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
            
            let newQty = parseInt(input.value);
            if (action === 'plus') {
                newQty++;
            } else if (action === 'minus' && newQty > 1) {
                newQty--;
            } else {
                return;
            }
            
            input.value = newQty;
            const newSubtotal = price * newQty;
            subtotalEl.textContent = '$' + newSubtotal.toFixed(2);
            updateSummary();
            
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
                    const currentQty = parseInt(input.value);
                    const revertQty = action === 'plus' ? currentQty - 1 : currentQty + 1;
                    input.value = revertQty;
                    subtotalEl.textContent = '$' + (price * revertQty).toFixed(2);
                    updateSummary();
                    alert('Error al actualizar cantidad');
                }
            } catch (error) {
                const currentQty = parseInt(input.value);
                const revertQty = action === 'plus' ? currentQty - 1 : currentQty + 1;
                input.value = revertQty;
                subtotalEl.textContent = '$' + (price * revertQty).toFixed(2);
                updateSummary();
                alert('Error de conexión');
            }
        });

        // ===== Evento: Botón eliminar =====
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

        // ===== Evento: Botón seleccionar/deseleccionar =====
        document.addEventListener('click', async function(e) {
            const btn = e.target.closest('.btn-select');
            if (!btn) return;

            e.preventDefault();
            
            const item = btn.closest('.cart-item');
            const libroId = item.dataset.libroId;
            
            const isCurrentlySelected = item.classList.contains('selected');
            const newState = isCurrentlySelected ? 0 : 1;

            item.classList.toggle('selected');
            btn.classList.toggle('selected');
            btn.textContent = isCurrentlySelected ? 'Seleccionar' : 'Deseleccionar';
            
            updateSummary();

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
                    item.classList.toggle('selected');
                    btn.classList.toggle('selected');
                    btn.textContent = isCurrentlySelected ? 'Deseleccionar' : 'Seleccionar';
                    updateSummary();
                    alert('Error al actualizar selección: ' + (data.error || 'Error desconocido'));
                }
            } catch (error) {
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