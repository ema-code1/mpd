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

<h2>Libros con Stock</h2>
<table class="excel-table">
    <thead>
        <tr>
            <th>Titulo</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $tieneStock = array_filter($libros, fn($l) => $l['stock'] >= 1);
        if (!empty($tieneStock)):
            foreach ($tieneStock as $libro): ?>
                <tr>
                    <td><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td><?= htmlspecialchars($libro['stock']) ?></td>
                </tr>
            <?php endforeach;
        else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No hay libros con stock disponible</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- <h2>Libros Sin Stock</h2>
<table class="excel-table">
    <thead>
        <tr>
            <th>Titulo</th>
            <th>Autor</th>
            <th>Edicion</th>
            <th>Precio</th>
            <th>Categoria</th>
            <th>Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sinStock = array_filter($libros, fn($l) => $l['stock'] == 0);
        if (!empty($sinStock)):
            foreach ($sinStock as $libro): ?>
                <tr>
                    <td><?= htmlspecialchars($libro['titulo']) ?></td>
                    <td><?= htmlspecialchars($libro['autor']) ?></td>
                    <td><?= htmlspecialchars($libro['edicion']) ?></td>
                    <td>$<?= number_format($libro['precio'], 2) ?></td>
                    <td><?= htmlspecialchars($libro['categoria']) ?></td>
                    <td><?= htmlspecialchars($libro['stock']) ?></td>
                </tr>
            <?php endforeach;
        else: ?>
            <tr>
                <td colspan="6" style="text-align:center;">No hay libros sin stock</td>
            </tr>
        <?php endif; ?> -->
    </tbody>
</table>

        </div>
    </div>

</body>
</html>