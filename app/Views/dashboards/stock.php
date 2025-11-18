<?php
// Ejemplo: $libros = $libroModel->findAll(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libros - Tabla tipo Excel</title>
    <link rel="stylesheet" href="<?= base_url('styles/stock.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
</head>
<body>

  <!-- BOTON MENU -->
  <i class="ti ti-menu-2 menu-btn" id="menu-btn"></i>
  
  <!-- SIDEBAR -->
  <div class="sidebar" id="sidebar">
    <button class="close-btn" id="close-btn">
      <i class="ti ti-x"></i>
    </button>
    <h3><i class="ti ti-dashboard"></i> Dashboard</h3>
    <a href="<?= base_url('index.php/panel')?>"><i class="ti ti-chart-bar"></i> Gráficos</a>
    <a href="<?= site_url('stock')?>"><i class="ti ti-books"></i> Stock</a>
    <a href="<?= site_url('upload_book') ?>"><i class="ti ti-book-upload"></i> Cargar nuevo libro</a>
    <a href="#"><i class="ti ti-shopping-cart"></i> Actividad de compras</a>
    <a href="<?= site_url('movimientos') ?>"><i class="ti ti-transfer"></i> Movimientos</a>
  </div>

  <div class="content" id="content">
    <div class="card fade-in">
      <h1>Stock de Libros</h1>

      <div class="table-header">
        <button class="add-column-btn" id="add-column-btn">
          <i class="ti ti-plus"></i>
          <span>Agregar Ingreso</span>
        </button>

        <!-- Botón nuevo para EGRESO (está a la derecha) -->
        <button class="add-column-btn add-egreso-btn" id="add-egreso-btn" style="margin-left:8px;">
          <i class="ti ti-minus"></i>
          <span>Agregar Egreso</span>
        </button>
      </div>

      <div class="table-container">
        <!-- Columna Título (FIJA IZQUIERDA) -->
        <table class="excel-table fixed-title-column">
          <thead>
            <tr>
              <th>Título</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($libros)): ?>
              <?php foreach ($libros as $libro): ?>
                <tr>
                  <td><?= htmlspecialchars($libro['titulo']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td style="text-align:center;">No hay libros cargados</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- COLUMNAS SCROLLABLES (MEDIO) - Ingresos/Egresos -->
        <div class="scrollable-columns-container">
          <div class="scrollable-columns" id="scrollable-columns">
            <?php if (!empty($cols)): ?>
              <?php foreach ($cols as $col): ?>
                <table class="excel-table dynamic-column <?= $col['tipo'] ?>-column <?= $col['bloqueado'] ? 'locked' : '' ?>" 
                       data-column-id="<?= $col['id'] ?>" 
                       data-bloqueado="<?= $col['bloqueado'] ?>">
                  <thead>
                    <tr>
                      <th>
                        <div class="column-header">
                          <span class="column-name">
                            <?= htmlspecialchars($col['name']) ?>
                          </span>
                          <div class="column-actions">
                            <!-- 🔒 BOTÓN DE CANDADO -->
                            <button class="lock-btn <?= $col['bloqueado'] ? 'locked' : '' ?>" 
                                    data-column-id="<?= $col['id'] ?>"
                                    title="<?= $col['bloqueado'] ? 'Desbloquear columna' : 'Bloquear columna' ?>">
                              <i class="<?= $col['bloqueado'] ? 'ti ti-lock' : 'ti ti-lock-open' ?>"></i>
                            </button>
                            
                            <button class="column-menu-btn" data-column-id="<?= $col['id'] ?>">
                              <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="column-dropdown" id="dropdown-<?= $col['id'] ?>">
                              <a href="#" class="dropdown-item delete-column" data-column-id="<?= $col['id'] ?>">
                                <i class="ti ti-trash"></i>
                                Eliminar campo
                              </a>
                            </div>
                          </div>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($libros as $libro): ?>
                      <?php 
                      $valor = isset($values[$col['id']][$libro['id']]) ? $values[$col['id']][$libro['id']] : 0;
                      ?>
                      <tr>
                        <td>
                          <div class="quantity-control">
                            <button type="button" class="btn-quantity minus-btn" 
                                    data-column-id="<?= $col['id'] ?>" 
                                    data-libro-id="<?= $libro['id'] ?>"
                                    <?= $col['bloqueado'] ? 'disabled' : '' ?>>-</button>
                            <input type="number" class="quantity-input" 
                                   value="<?= $valor ?>" min="0" readonly>
                            <button type="button" class="btn-quantity plus-btn" 
                                    data-column-id="<?= $col['id'] ?>" 
                                    data-libro-id="<?= $libro['id'] ?>"
                                    <?= $col['bloqueado'] ? 'disabled' : '' ?>>+</button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <!-- Columna Stock (FIJA DERECHA) -->
        <table class="excel-table fixed-stock-column">
          <thead>
            <tr>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($libros)): ?>
              <?php foreach ($libros as $libro): ?>
                <tr>
                  <td class="stock-value" data-libro-id="<?= $libro['id'] ?>">
                    <?= htmlspecialchars($libro['stock']) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td style="text-align:center;">-</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <?php 
$esEgreso = $col['tipo'] === 'egreso';
$stockDisponible = (int)$libro['stock'];
$deshabilitarMas = $esEgreso && $stockDisponible <= 0;
?>

<button ... 
    class="btn-quantity plus-btn <?= $deshabilitarMas ? 'no-stock' : '' ?>"
    data-tipo="<?= $col['tipo'] ?>"
    data-stock="<?= $stockDisponible ?>"
    <?= ($col['bloqueado'] || $deshabilitarMas) ? 'disabled' : '' ?>
    title="<?= $deshabilitarMas ? 'Sin stock disponible' : '' ?>">
+
</button>

  <script>
    // DEBUG: Verificar que el JavaScript se carga
    console.log('Stock script loaded');

    // Manejar botones de cantidad - VERSIÓN SIMPLIFICADA
    document.addEventListener('click', function(e) {
      console.log('Click detected:', e.target);
      
      // 🔒 BOTÓN DE CANDADO
      if (e.target.closest('.lock-btn')) {
        const lockBtn = e.target.closest('.lock-btn');
        const columnId = lockBtn.dataset.columnId;
        
        toggleColumnLock(columnId, lockBtn);
        e.stopPropagation();
        return;
      }
      
      // Botones de cantidad
      if (e.target.classList.contains('btn-quantity')) {
        const btn = e.target;
        const columnId = btn.dataset.columnId;
        const libroId = btn.dataset.libroId;
        const isPlus = btn.classList.contains('plus-btn');
        
        console.log('Quantity button clicked:', { columnId, libroId, isPlus });
        
        if (columnId && libroId) {
          const change = isPlus ? 1 : -1;
          updateQuantity(parseInt(columnId), parseInt(libroId), change);
        }
        return;
      }

      // Dropdown menus
      if (e.target.closest('.column-menu-btn')) {
        const menuBtn = e.target.closest('.column-menu-btn');
        const columnId = menuBtn.dataset.columnId;
        const dropdown = document.getElementById(`dropdown-${columnId}`);
        
        // Cerrar todos los demás dropdowns
        document.querySelectorAll('.column-dropdown.active').forEach(dd => {
          if (dd !== dropdown) dd.classList.remove('active');
        });
        
        // Toggle el dropdown actual
        dropdown.classList.toggle('active');
        e.stopPropagation();
        return;
      }

      // Eliminar columna
      if (e.target.closest('.delete-column')) {
        const deleteBtn = e.target.closest('.delete-column');
        const columnId = deleteBtn.dataset.columnId;
        
        if (confirm('¿Estás seguro de que quieres eliminar esta columna? Se perderán todos los datos.')) {
          deleteColumn(columnId);
        }
        e.stopPropagation();
        return;
      }

      // Cerrar dropdowns al hacer click fuera
      if (!e.target.closest('.column-dropdown') && !e.target.closest('.column-menu-btn')) {
        document.querySelectorAll('.column-dropdown.active').forEach(dropdown => {
          dropdown.classList.remove('active');
        });
      }
    });

    // 🔒 FUNCIÓN PARA BLOQUEAR/DESBLOQUEAR COLUMNA
    function toggleColumnLock(columnId, lockBtn) {
      fetch('<?= site_url('stock/toggleLock') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `column_id=${columnId}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          const column = document.querySelector(`.dynamic-column[data-column-id="${columnId}"]`);
          const icon = lockBtn.querySelector('i');
          
          if (data.bloqueado === 1) {
            // Bloquear
            lockBtn.classList.add('locked');
            icon.className = 'ti ti-lock';
            lockBtn.title = 'Desbloquear columna';
            column.classList.add('locked');
            column.setAttribute('data-bloqueado', '1');
            
            // Deshabilitar botones
            column.querySelectorAll('.btn-quantity').forEach(btn => {
              btn.disabled = true;
            });
          } else {
            // Desbloquear
            lockBtn.classList.remove('locked');
            icon.className = 'ti ti-lock-open';
            lockBtn.title = 'Bloquear columna';
            column.classList.remove('locked');
            column.setAttribute('data-bloqueado', '0');
            
            // Habilitar botones
            column.querySelectorAll('.btn-quantity').forEach(btn => {
              btn.disabled = false;
            });
          }
          
          // Mostrar mensaje
          showToast(data.message);
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Lock toggle error:', error);
        alert('Error al cambiar el estado de bloqueo');
      });
    }

    // Función auxiliar para mostrar mensajes toast
    function showToast(message) {
      // Crear elemento toast si no existe
      let toast = document.getElementById('toast-message');
      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-message';
        toast.style.cssText = `
          position: fixed;
          top: 20px;
          right: 20px;
          background: #333;
          color: white;
          padding: 12px 24px;
          border-radius: 4px;
          z-index: 10000;
          opacity: 0;
          transition: opacity 0.3s ease;
        `;
        document.body.appendChild(toast);
      }
      
      toast.textContent = message;
      toast.style.opacity = '1';
      
      setTimeout(() => {
        toast.style.opacity = '0';
      }, 2500);
    }

    // Actualizar cantidad en la base de datos
    function updateQuantity(columnId, libroId, delta) {
      fetch('<?= site_url('stock/updateCell') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `column_id=${columnId}&libro_id=${libroId}&delta=${delta}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          // 1. Actualizar el input de la celda modificada
          const inputs = document.querySelectorAll(`.btn-quantity[data-column-id="${columnId}"][data-libro-id="${libroId}"]`);
          inputs.forEach(btn => {
            const input = btn.closest('.quantity-control').querySelector('.quantity-input');
            input.value = data.new_value;
          });
          
          // 2. ACTUALIZAR EL STOCK EN TIEMPO REAL (usando el valor del backend)
          const stockElements = document.querySelectorAll(`.stock-value[data-libro-id="${libroId}"]`);
          stockElements.forEach(element => {
            element.textContent = data.nuevo_stock;
          });
        } else if (data.status === 'error') {
          alert(data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    }

    // Actualizar display del stock
    function updateStockDisplay(libroId) {
      fetch('<?= site_url('stock/getStock') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `libro_id=${libroId}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          const stockElements = document.querySelectorAll(`.stock-value[data-libro-id="${libroId}"]`);
          stockElements.forEach(element => {
            element.textContent = data.stock;
          });
        }
      })
      .catch(error => console.error('Stock display error:', error));
    }

    // Agregar nueva columna
    document.getElementById('add-column-btn').addEventListener('click', function() {
      const name = prompt('Nombre del ingreso:', 'Ingreso ' + new Date().toLocaleDateString());
      if (name) {
        fetch('<?= site_url('stock/createColumn') ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `name=${encodeURIComponent(name)}&tipo=ingreso`
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'ok') {
            location.reload();
          } else {
            alert('Error al crear la columna: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Create column error:', error);
          alert('Error de conexión al crear la columna');
        });
      }
    });

    // Listener para "Agregar Egreso"
    document.getElementById('add-egreso-btn').addEventListener('click', function() {
      const name = prompt('Nombre del egreso:', 'Egreso ' + new Date().toLocaleDateString());
      if (!name) return;

      fetch('<?= site_url('stock/createColumn') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `name=${encodeURIComponent(name)}&tipo=egreso`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          location.reload();
        } else {
          alert('Error al crear la columna: ' + (data.message || 'error desconocido'));
        }
      })
      .catch(err => {
        console.error('Create egreso error:', err);
        alert('Error de conexión al crear la columna (egreso)');
      });
    });

    // Eliminar columna
    function deleteColumn(columnId) {
      console.log('Deleting column:', columnId);
      
      fetch('<?= site_url('stock/deleteColumn') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${columnId}`
      })
      .then(response => response.json())
      .then(data => {
        console.log('Delete response:', data);
        if (data.status === 'ok') {
          // Remover la columna del DOM
          const column = document.querySelector(`.dynamic-column[data-column-id="${columnId}"]`);
          if (column) {
            column.remove();
          }
          // Recargar para actualizar cálculos de stock
          location.reload();
        } else {
          alert('Error al eliminar la columna: ' + (data.msg || 'Error desconocido'));
        }
      })
      .catch(error => {
        console.error('Delete error:', error);
        alert('Error de conexión al eliminar la columna');
      });
    }

    // Menu toggle para móviles
    document.getElementById('menu-btn')?.addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('active');
    });

    document.getElementById('close-btn')?.addEventListener('click', function() {
      document.getElementById('sidebar').classList.remove('active');
    });

    // Scroll automático a la derecha al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
      const scrollContainer = document.querySelector('.scrollable-columns-container');
      if (scrollContainer) {
        setTimeout(() => {
          scrollContainer.scrollLeft = scrollContainer.scrollWidth;
        }, 100);
      }
    });

    //Botones sin Stock
    function updateEgresoButtons(libroId, nuevoStock) {
  const egresoButtons = document.querySelectorAll(
    `.plus-btn[data-libro-id="${libroId}"][data-tipo="egreso"]`
  );
  
  egresoButtons.forEach(btn => {
    if (nuevoStock <= 0) {
      btn.disabled = true;
      btn.classList.add('no-stock');
      btn.title = 'Sin stock disponible';
    } else {
      // Habilitar solo si la columna no está bloqueada
      const column = btn.closest('.dynamic-column');
      const isBloqueada = column.getAttribute('data-bloqueado') === '1';
      
      if (!isBloqueada) {
        btn.disabled = false;
        btn.classList.remove('no-stock');
        btn.title = '';
      }
    }
  });
}
  </script>

</body>
</html>