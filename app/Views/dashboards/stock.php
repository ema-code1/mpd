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
    <a href="#"><i class="ti ti-chart-bar"></i> Gráficos</a>
    <a href="<?= site_url('stock_spreadsheet')?>"><i class="ti ti-books"></i> Stock</a>
    <a href="<?= site_url('upload_book') ?>"><i class="ti ti-book-upload"></i> Cargar nuevo libro</a>
    <a href="#"><i class="ti ti-shopping-cart"></i> Actividad de compras</a>
    <a href="#"><i class="ti ti-transfer"></i> Movimientos</a>
  </div>

  <div class="content" id="content">
    <div class="card fade-in">
      <h1>Stock de Libros</h1>

      <div class="table-header">
        <button class="add-column-btn" id="add-column-btn">
          <i class="ti ti-plus"></i>
          <span>Agregar Ingreso</span>
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
                <table class="excel-table dynamic-column <?= $col['tipo'] ?>-column" data-column-id="<?= $col['id'] ?>">
                  <thead>
                    <tr>
                      <th>
                        <div class="column-header">
                          <span class="column-name">
                            <?= htmlspecialchars($col['name']) ?>
                            <span class="column-type">(<?= $col['tipo'] ?>)</span>
                          </span>
                          <div class="column-actions">
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
                            <button type="button" class="btn-quantity" 
                                    data-column-id="<?= $col['id'] ?>" 
                                    data-libro-id="<?= $libro['id'] ?>" 
                                    data-action="minus">-</button>
                            <input type="number" class="quantity-input" 
                                   value="<?= $valor ?>" min="0" readonly>
                            <button type="button" class="btn-quantity" 
                                    data-column-id="<?= $col['id'] ?>" 
                                    data-libro-id="<?= $libro['id'] ?>" 
                                    data-action="plus">+</button>
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

  <script>
    // Manejar botones de cantidad - VERSIÓN CORREGIDA
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.btn-quantity');
      if (!btn) return;

      const columnId = btn.dataset.columnId;
      const libroId = btn.dataset.libroId;
      const action = btn.dataset.action;
      
      if (columnId && libroId && (action === 'plus' || action === 'minus')) {
        const change = (action === 'plus') ? 1 : -1;
        updateQuantity(parseInt(columnId), parseInt(libroId), change);
      }
    });

    // Actualizar cantidad en la base de datos
    function updateQuantity(columnId, libroId, delta) {
      console.log('Updating:', columnId, libroId, delta); // Debug
      
      fetch('<?= site_url('stock/updateCell') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `column_id=${columnId}&libro_id=${libroId}&delta=${delta}`
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        console.log('Response:', data); // Debug
        if (data.status === 'ok') {
          // Actualizar el input de la celda
          const inputs = document.querySelectorAll(`.btn-quantity[data-column-id="${columnId}"][data-libro-id="${libroId}"]`);
          inputs.forEach(btn => {
            const input = btn.closest('.quantity-control').querySelector('.quantity-input');
            input.value = data.new_value;
          });
          
          // Actualizar stock general
          updateStockDisplay(libroId);
        } else {
          console.error('Server error:', data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar la cantidad');
      });
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
      .catch(error => console.error('Error:', error));
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
          console.error('Error:', error);
          alert('Error al crear la columna');
        });
      }
    });

    // Dropdown para eliminar columnas
    document.addEventListener('click', function(e) {
      // Abrir/cerrar dropdown
      const menuBtn = e.target.closest('.column-menu-btn');
      if (menuBtn) {
        const columnId = menuBtn.dataset.columnId;
        const dropdown = document.getElementById(`dropdown-${columnId}`);
        const allDropdowns = document.querySelectorAll('.column-dropdown');
        
        // Cerrar todos los demás dropdowns
        allDropdowns.forEach(dd => {
          if (dd !== dropdown) {
            dd.classList.remove('active');
          }
        });
        
        // Toggle el dropdown actual
        dropdown.classList.toggle('active');
        e.stopPropagation();
        return;
      }

      // Eliminar columna
      const deleteBtn = e.target.closest('.delete-column');
      if (deleteBtn) {
        const columnId = deleteBtn.dataset.columnId;
        if (confirm('¿Estás seguro de que quieres eliminar esta columna? Se perderán todos los datos.')) {
          deleteColumn(columnId);
        }
        e.stopPropagation();
        return;
      }

      // Cerrar dropdowns al hacer click fuera
      const dropdowns = document.querySelectorAll('.column-dropdown.active');
      dropdowns.forEach(dropdown => {
        if (!dropdown.contains(e.target)) {
          dropdown.classList.remove('active');
        }
      });
    });

    // Eliminar columna
    function deleteColumn(columnId) {
      fetch('<?= site_url('stock/deleteColumn') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${columnId}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'ok') {
          // Remover la columna del DOM
          const column = document.querySelector(`.dynamic-column[data-column-id="${columnId}"]`);
          if (column) {
            column.remove();
          }
          // Recargar para actualizar cálculos de stock
          location.reload();
        } else {
          alert('Error al eliminar la columna: ' + data.msg);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la columna');
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
        scrollContainer.style.scrollBehavior = 'smooth';
      }
    });
  </script>

</body>
</html>