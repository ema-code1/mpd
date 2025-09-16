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
    <button id="btn-filtros" class="filter-label">
        Filtrar <i class="ti ti-adjustments-horizontal"></i>
    </button>
    <input type="text" id="buscador" class="search-input" placeholder="Buscar..." onkeyup="filtrarLibros()">
</div>
    
    <!-- Contenedor principal para los libros --> 
    <div class="cards-container">
        <div class="container-container">
            <div class="card-grid">
            <?php if(!empty($libros)): ?>
                <?php foreach($libros as $libro): ?>
                    <div class="card" 
                         data-titulo="<?= esc($libro['titulo']) ?>" 
                         data-autor="<?= esc($libro['autor']) ?>" 
                         data-edicion="<?= esc($libro['edicion'] ?? 'No especificada') ?>" 
                         data-precio="<?= esc(str_replace(',', '.', str_replace('$', '', $libro['precio']))) ?>"
                         data-categoria="<?= esc($libro['categoria']) ?>"
                         onclick="window.location.href='<?= site_url('libro/' . $libro['id']) ?>'" 
                         style="cursor: pointer;">   
                        <div class="card-img">
                            <?php if (!empty($libro['foto1'])): ?>
                                <img src="<?= base_url( $libro['foto1'])?>"
                                     alt="<?= base_url( $libro['foto1'])?>"
                                     class="book-image">
                            <?php else: ?>
                                <img src="<?= base_url('imgs/noimageavailable.jpg')?>" 
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

            document.addEventListener('DOMContentLoaded', function() {
        const optionsButtons = document.querySelectorAll('.options-btn');
        
        optionsButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const menu = this.nextElementSibling;
                const isShowing = menu.classList.contains('show');
                
                // Cerrar todos los menús abiertos
                document.querySelectorAll('.options-menu.show').forEach(openMenu => {
                    if (openMenu !== menu) {
                        openMenu.classList.remove('show');
                    }
                });
                
                // Abrir/cerrar el menú actual
                if (!isShowing) {
                    menu.classList.add('show');
                } else {
                    menu.classList.remove('show');
                }
            });
        });
        
        // Cerrar menús al hacer clic en cualquier parte del documento
        document.addEventListener('click', function() {
            document.querySelectorAll('.options-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });
    });
    </script>
    
    <!-- PANEL DE FILTROS LATERAL -->
<div class="panel-filtros" id="panel-filtros">
    <h3>Filtrar por</h3>
    <label>
        <input type="checkbox" name="filtro" value="titulo" checked> 
        <span class="span">Título</span>
    </label>
    <label>
        <input type="checkbox" name="filtro" value="autor" checked> 
        <span class="span">Autor</span>
    </label>
    <label>
        <input type="checkbox" name="filtro" value="edicion" checked> 
        <span class="span">Edición</span>
    </label>
    <div class="filtro-precio">
        <label>
            <input type="checkbox" name="filtro_precio" value="mayor" id="filtro_precio_mayor"> 
            <span class="masde">Más de</span>
            <input type="number" id="precio_mayor_valor" placeholder="Ej: 1000">
        </label>
        <label>
            <input type="checkbox" name="filtro_precio" value="menor" id="filtro_precio_menor"> 
            <span>Menos de</span>
            <input type="number" id="precio_menor_valor" placeholder="Ej: 500">
        </label>
        
    </div>
    <label>
        <input type="checkbox" name="filtro" value="categoria" checked> 
        <span class="span">Categoría</span>
    </label>
    <button onclick="filtrarLibros()">Aplicar Filtros</button>
    <button>Cerrar</button>
</div>




<script> //SCRIPT DE FILTRADO
  const btnFiltros = document.getElementById("btn-filtros");
  const panelFiltros = document.getElementById("panel-filtros");
  const overlayFiltros = document.getElementById("overlay-filtros");
  const closeBtns = panelFiltros.querySelectorAll("button, .cerrar-filtros");

  // Abrir o cerrar sidebar con el mismo botón
  btnFiltros.addEventListener("click", (e) => {
    e.stopPropagation();
    panelFiltros.classList.toggle("active");
    overlayFiltros.classList.toggle("active");
  });

  // Cerrar sidebar con botones internos
  closeBtns.forEach(btn => {
    btn.addEventListener("click", () => {
      panelFiltros.classList.remove("active");
      overlayFiltros.classList.remove("active");
    });
  });

  // Cerrar sidebar haciendo click fuera (en documento)
  document.addEventListener("click", () => {
    panelFiltros.classList.remove("active");
    overlayFiltros.classList.remove("active");
  });

  // Evitar cierre si clickeo dentro del panel
  panelFiltros.addEventListener("click", (e) => {
    e.stopPropagation();
  });



function filtrarLibros() {
    const texto = document.getElementById('buscador').value.toLowerCase();
    const checkboxes = document.querySelectorAll('input[name="filtro"]:checked');
    const filtrosActivos = Array.from(checkboxes).map(cb => cb.value);

    // Nuevos filtros de precio
    const filtroMenor = document.getElementById('filtro_precio_menor').checked;
    const filtroMayor = document.getElementById('filtro_precio_mayor').checked;
    const valorMenor = parseFloat(document.getElementById('precio_menor_valor').value) || 0;
    const valorMayor = parseFloat(document.getElementById('precio_mayor_valor').value) || 0;

    document.querySelectorAll('.card').forEach(item => {
        const titulo = item.getAttribute('data-titulo')?.toLowerCase() || '';
        const autor = item.getAttribute('data-autor')?.toLowerCase() || '';
        const edicion = item.getAttribute('data-edicion')?.toLowerCase() || '';
        const precio = parseFloat(item.getAttribute('data-precio')) || 0; // Convertimos a número
        const categoria = item.getAttribute('data-categoria')?.toLowerCase() || '';

        // Búsqueda por texto en campos seleccionados
        let coincideTexto = false;
        if (texto) {
            coincideTexto = filtrosActivos.some(filtro => {
                switch(filtro) {
                    case 'titulo': return titulo.includes(texto);
                    case 'autor': return autor.includes(texto);
                    case 'edicion': return edicion.includes(texto);
                    case 'categoria': return categoria.includes(texto);
                    default: return false;
                }
            });
        } else {
            // Si no hay texto, consideramos que coincide por texto (para que solo apliquen filtros de precio)
            coincideTexto = true;
        }

        // Filtro por precio numérico
        let coincidePrecio = true; // Por defecto no filtra si no están activos

        if (filtroMenor && filtroMayor) {
            coincidePrecio = precio < valorMenor && precio > valorMayor;
        } else if (filtroMenor) {
            coincidePrecio = precio < valorMenor;
        } else if (filtroMayor) {
            coincidePrecio = precio > valorMayor;
        }

        // Mostrar solo si coincide texto Y precio
        item.style.display = (coincideTexto && coincidePrecio) ? 'flex' : 'none';
    });
};
</script>
    
    <link rel="stylesheet" href="<?= base_url('styles/index.css') ?>">
</body>
</html>