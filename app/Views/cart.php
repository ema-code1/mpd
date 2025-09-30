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
                    <div class="cart-item" data-price="<?= $item['precio'] ?>" data-id="<?= $item['id'] ?>">
                        <div class="item-image">
                            <img src="<?= base_url($item['foto1'] ?? 'imgs/noimageavailable.jpg') ?>" alt="<?= esc($item['titulo']) ?>">
                        </div>
                        <div class="item-details">
                            <h2 class="item-title"><?= htmlspecialchars($item['titulo']) ?></h2>
                            <p class="item-price-unit">$<?= number_format($item['precio'], 2) ?></p>

                            <div class="item-controls">
                                <div class="quantity-control">
                                    <button class="btn-quantity" data-action="minus">-</button>
                                    <input type="text" value="<?= $item['cantidad'] ?? 1 ?>" class="quantity-input">
                                    <button class="btn-quantity" data-action="plus">+</button>
                                </div>
                                <p class="item-price-total">$<?= number_format($item['precio'], 2) ?></p>
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

<!-- SCRIPT (mismo que te pasé antes) -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.cart-items');
  const buyBtn = document.querySelector('.btn-buy');

  function updateSummary(){
    const items = document.querySelectorAll('.cart-item'); // dinámico
    let total = 0;
    let selectedCount = 0;

    items.forEach(item => {
      const qtyInput = item.querySelector('.quantity-input');
      const quantity = parseInt(qtyInput.value) || 0;
      const priceUnit = parseFloat(item.dataset.price) || 0;

      if (item.classList.contains('selected')) {
        selectedCount += quantity; // si querés contar títulos en vez de cantidad, cambia esto por selectedCount += 1;
        total += priceUnit * quantity;
      }

      const subtotalEl = item.querySelector('.item-price-total');
      if (subtotalEl) subtotalEl.textContent = '$' + (priceUnit * quantity).toFixed(2);
    });

    const selEl = document.getElementById('selected-count');
    const totalEl = document.getElementById('summary-total');
    if (selEl) selEl.textContent = selectedCount;
    if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
    if (buyBtn) buyBtn.disabled = selectedCount === 0;
  }

  // Delegación de eventos para +, -, seleccionar y eliminar
  if (container) {
    container.addEventListener('click', (e) => {
      const btn = e.target.closest('button');
      if (!btn) return;
      const item = btn.closest('.cart-item');
      if (!item) return;

      // + / -
      if (btn.classList.contains('btn-quantity')) {
        const action = btn.dataset.action;
        const input = item.querySelector('.quantity-input');
        let v = parseInt(input.value) || 0;
        if (action === 'plus') v++;
        if (action === 'minus' && v > 1) v--;
        input.value = v;
        updateSummary();
        return;
      }

      // seleccionar
      if (btn.classList.contains('btn-select')) {
        btn.classList.toggle('active');
        item.classList.toggle('selected');
        updateSummary();
        return;
      }

      // eliminar
      if (btn.classList.contains('btn-remove')) {
        if (!confirm('¿Querés borrar este producto del carrito?')) return;
        const id = item.dataset.id;
        if (!id) {
          alert('No hay id del producto. Fijate el atributo data-id en el cart-item.');
          return;
        }
        
        // Hacer la petición AJAX para eliminar
        fetch('<?= base_url('cart/delete') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'item_id=' + id
        })
        .then(response => response.json())
        .then(json => {
          if (json.success) {
            // Eliminar el elemento del DOM
            item.remove();
            updateSummary();
            alert('Producto eliminado correctamente');
          } else {
            alert(json.error || 'No se pudo borrar el item');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error de red. No se pudo borrar el item.');
        });
      }
    });

    // Cambios manuales en el input de cantidad
    container.addEventListener('change', (e) => {
      if (e.target && e.target.classList.contains('quantity-input')) {
        if (parseInt(e.target.value) < 1) e.target.value = 1;
        updateSummary();
      }
    });
  }

  updateSummary();
});
</script>

</body>
</html>