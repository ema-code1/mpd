<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editorial MPD - Inicio</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
</head>
<body>
    <div class="filter-bar">
        <span class="filter-label">Filtrar <i class="ti ti-adjustments-horizontal"></i></span>
        <input type="text" class="search-input" placeholder="Buscar...">
    </div>
    
    <!-- Contenedor principal para los libros --> 
    <div class="cards-container">
        <div class="container-container">
            <div class="card-grid">
            <?php if(!empty($libros)): ?>
                <?php foreach($libros as $libro): ?>
                    <div class="card" onclick="window.location.href='<?= site_url('libro/' . $libro['id']) ?>'" style="cursor: pointer;">   
                        <div class="card-img">
                            <?php if (!empty($libro['foto1'])): ?>
                                <img src="<?= base_url( $libro['foto1'])?>"
                                     alt="<?= esc($libro['titulo']) ?>"
                                     class="book-image">
                            <?php else: ?>
                                <img src="<?= base_url('imgs/noimageavailable.jpg') ?>" 
                                     alt="Libro sin imagen"
                                     class="card-img">
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title"><?= esc($libro['titulo']) ?></h3>
                            <p class="card-price">$<?= number_format(esc($libro['precio']), 2) ?></p>
                            <ul class="card-desc">
                                <li><strong>Autor:</strong> <?= esc($libro['autor']) ?></li>
                                <li><strong>Edición:</strong> <?= esc($libro['edicion'] ?? 'No especificada') ?></li>
                                <li><strong>Categoría:</strong> <?= esc($libro['categoria']) ?></li>
                                <li><?= esc(substr($libro['descripcion'], 0, 100) . (strlen($libro['descripcion']) > 100 ? '...' : '')) ?></li>
                            </ul>
                            <div class="card-footer">
                                <a href="<?= site_url('libro/' . $libro['id']) ?>" class="btn-detail">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-books">
                    <i class="ti ti-books"></i>
                    <p>No hay libros en stock.</p>
                </div>
            <?php endif; ?>
        </div>
        </div>
    </div>
    
    <script>
        window.addEventListener("load", () => {
            document.querySelectorAll(".card").forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add("active");
                }, 100 * index);
            });
        });
    </script>
    
    <link rel="stylesheet" href="<?= base_url('styles/index.css') ?>">
</body>
</html>