<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de Ventas - MPD</title>
    <link rel="stylesheet" href="<?= base_url('styles/movimientos.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <a href="<?= site_url('stock_spreadsheet')?>"><i class="ti ti-books"></i> Stock</a>
    <a href="<?= site_url('upload_book') ?>"><i class="ti ti-book-upload"></i> Cargar nuevo libro</a>
    <a href="#"><i class="ti ti-shopping-cart"></i> Actividad de compras</a>
    <a href="<?= site_url('movimientos') ?>"><i class="ti ti-transfer"></i> Movimientos</a>
  </div>

    <div class="content">
        <div class="card fade-in">
            <h1><i class="fas fa-chart-line"></i> Movimientos de Ventas</h1>
            <p class="subtitle">Resumen completo de todas las transacciones realizadas</p>
            
            <!-- Estadísticas -->
            <div class="stats-container">
                <div class="stat-card slide-in">
                    <div class="stat-value">$<?= number_format($totalVentas ?? 0, 2) ?></div>
                    <div class="stat-label">Total en Ventas</div>
                </div>
                <div class="stat-card slide-in" style="animation-delay: 0.1s">
                    <div class="stat-value"><?= count($movimientos ?? []) ?></div>
                    <div class="stat-label">Transacciones Totales</div>
                </div>
                <div class="stat-card slide-in" style="animation-delay: 0.2s">
                    <div class="stat-value"><?= count($ventasPorMes ?? []) ?></div>
                    <div class="stat-label">Meses con Actividad</div>
                </div>
            </div>

            <!-- Tabla de movimientos -->
            <div class="table-container">
                <table class="excel-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID Venta</th>
                            <th class="col-comprador">Comprador</th>
                            <th class="col-metodo">Método de Pago</th>
                            <th class="col-estado">Estado</th>
                            <th class="col-monto">Monto</th>
                            <th class="col-fecha">Fecha Pago</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $movimientos = $movimientos ?? [];
                        foreach ($movimientos as $mov): 
                        ?>
                        <tr class="slide-in venta-row" data-venta-id="<?= $mov['venta_id'] ?>">
                            <td>#<?= $mov['venta_id'] ?></td>
                            <td>
                                <strong><?= esc($mov['comprador']) ?></strong>
                                <br><small>ID: <?= $mov['comprador_id'] ?></small>
                            </td>
                            <td class="col-metodo">
                                <?php 
                                $metodo = $mov['met_pago'] ?? 'efectivo';
                                $badgeClass = $metodo === 'transferencia' ? 'badge-info' : 'badge-warning';
                                $text = $metodo === 'transferencia' ? 'Transferencia' : 'Efectivo';
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $text ?></span>
                            </td>
                            <td>
                                <?php 
                                $estado = $mov['estado'] ?? 'completada';
                                $estadoBadge = $estado === 'completada' ? 'badge-success' : 'badge-warning';
                                ?>
                                <span class="badge <?= $estadoBadge ?>"><?= ucfirst($estado) ?></span>
                            </td>
                            <td class="col-monto monto-positivo">
                                $<?= number_format($mov['total_venta'], 2) ?>
                            </td>
                            <td class="col-fecha">
                                <?= date('d/m/Y', strtotime($mov['fecha_de_pago'])) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Tooltip que sigue al cursor -->
            <div id="row-tooltip" class="row-tooltip"></div>

            <?php if (empty($movimientos)): ?>
                <div style="text-align: center; padding: 3rem; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <h3>No hay movimientos registrados</h3>
                    <p>No se han encontrado ventas en el sistema.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.excel-table tr');
        
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });

        // Crear el input de búsqueda simple
        const searchGroup = document.createElement('div');
        searchGroup.className = 'search-group';
        
        const filterInput = document.createElement('input');
        filterInput.type = 'text';
        filterInput.placeholder = 'Buscar en movimientos...';
        filterInput.className = 'filter-input';
        
        searchGroup.appendChild(filterInput);
        
        const tableContainer = document.querySelector('.table-container');
        tableContainer.parentNode.insertBefore(searchGroup, tableContainer);

        filterInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.excel-table tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // ============================================
        // TOOLTIP CON DETALLES DE LA VENTA (AJAX)
        // ============================================
        const tooltip = document.getElementById('row-tooltip');
        const ventaRows = document.querySelectorAll('.venta-row');
        let currentVentaId = null;
        let tooltipTimeout = null;

        ventaRows.forEach(row => {
            row.addEventListener('mouseenter', function(e) {
                const ventaId = this.dataset.ventaId;
                
                // Cancelar timeout anterior
                if (tooltipTimeout) clearTimeout(tooltipTimeout);
                
                // Mostrar tooltip con "Cargando..."
                tooltip.innerHTML = '<div class="tooltip-title">Detalles de la venta #' + ventaId + '</div><div class="tooltip-loading">Cargando...</div>';
                tooltip.classList.add('active');
                
                // Posicionar tooltip
                positionTooltip(e);
                
                // Cargar detalles si es necesario
                if (currentVentaId !== ventaId) {
                    currentVentaId = ventaId;
                    loadDetalles(ventaId);
                }
            });

            row.addEventListener('mousemove', function(e) {
                positionTooltip(e);
            });

            row.addEventListener('mouseleave', function() {
                // Delay para ocultar tooltip
                tooltipTimeout = setTimeout(() => {
                    tooltip.classList.remove('active');
                    currentVentaId = null;
                }, 300);
            });
        });

        function positionTooltip(e) {
            const offsetX = 15;
            const offsetY = 15;
            
            let left = e.pageX + offsetX;
            let top = e.pageY + offsetY;
            
            // Evitar que se salga de la pantalla
            const tooltipRect = tooltip.getBoundingClientRect();
            if (left + tooltipRect.width > window.innerWidth) {
                left = e.pageX - tooltipRect.width - offsetX;
            }
            if (top + tooltipRect.height > window.innerHeight) {
                top = e.pageY - tooltipRect.height - offsetY;
            }
            
            tooltip.style.left = left + 'px';
            tooltip.style.top = top + 'px';
        }

        function loadDetalles(ventaId) {
            fetch('<?= base_url('movimientos/detalles') ?>/' + ventaId)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        let html = '<div class="tooltip-title">Detalles de la venta #' + ventaId + '</div>';
                        data.forEach(item => {
                            html += '<div class="tooltip-item">';
                            html += '<strong>' + item.titulo + '</strong><br>';
                            html += 'Cantidad: ' + item.cantidad + ' | ';
                            html += 'Precio unitario: $' + parseFloat(item.precio_unitario).toFixed(2) + '<br>';
                            html += 'Subtotal: $' + parseFloat(item.subtotal).toFixed(2);
                            html += '</div>';
                        });
                        tooltip.innerHTML = html;
                    } else {
                        tooltip.innerHTML = '<div class="tooltip-title">Detalles de la venta #' + ventaId + '</div><div class="tooltip-error">No se encontraron detalles</div>';
                    }
                })
                .catch(error => {
                    console.error('Error cargando detalles:', error);
                    tooltip.innerHTML = '<div class="tooltip-title">Detalles de la venta #' + ventaId + '</div><div class="tooltip-error">Error al cargar detalles</div>';
                });
        }
    });
    </script>
</body>
</html>