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
    <button id="btn-filtros" class="filter-label" style="background: none; border: none; cursor: pointer; font: inherit; color: inherit; display: flex; align-items: center; gap: 5px;">
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
                            <div class="card-options">
                                <button class="options-btn" title="Más opciones">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="options-menu">
                                    <a href="<?= site_url('libro/editar/' . $libro['id']) ?>" class="option-item">
                                        <i class="ti ti-edit"></i>
                                        <span>Editar publicación</span>
                                    </a>
                                </div>
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
<div id="panel-filtros" style="position: fixed; top: 0; left: -300px; width: 300px; height: 100vh; background: #f9f9f9; box-shadow: 2px 0 10px rgba(0,0,0,0.1); padding: 20px; transition: left 0.3s ease; z-index: 1000; overflow-y: auto;">
    <h3 style="margin-top: 0; padding-bottom: 10px; border-bottom: 1px solid #ddd; font-family: inherit;">Filtros de búsqueda</h3>
    <label style="display: block; margin: 10px 0; cursor: pointer; font-family: inherit;">
        <input type="checkbox" name="filtro" value="titulo" checked> Título
    </label>
    <label style="display: block; margin: 10px 0; cursor: pointer; font-family: inherit;">
        <input type="checkbox" name="filtro" value="autor" checked> Autor
    </label>
    <label style="display: block; margin: 10px 0; cursor: pointer; font-family: inherit;">
        <input type="checkbox" name="filtro" value="edicion" checked> Edición
    </label>
    <div style="margin: 15px 0; padding: 10px; background: #f1f1f1; border-radius: 8px; font-family: inherit;">
    <label style="display: block; margin: 5px 0; cursor: pointer;">
        <input type="checkbox" name="filtro_precio" value="menor" id="filtro_precio_menor"> Precio menor a:
        <input type="number" id="precio_menor_valor" placeholder="Ej: 500" style="width: 80px; padding: 3px 5px; margin-left: 10px; border: 1px solid #ccc; border-radius: 4px;">
    </label>
    <label style="display: block; margin: 5px 0; cursor: pointer;">
        <input type="checkbox" name="filtro_precio" value="mayor" id="filtro_precio_mayor"> Precio mayor a:
        <input type="number" id="precio_mayor_valor" placeholder="Ej: 1000" style="width: 80px; padding: 3px 5px; margin-left: 10px; border: 1px solid #ccc; border-radius: 4px;">
    </label>
    </div>
    <label style="display: block; margin: 10px 0; cursor: pointer; font-family: inherit;">
        <input type="checkbox" name="filtro" value="categoria" checked> Categoría
    </label>
    <button onclick="filtrarLibros()" style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 5px; margin-top: 20px; cursor: pointer; font-family: inherit;">Aplicar Filtros</button>
    <button onclick="toggleFiltros()" style="width: 100%; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; font-family: inherit;">Cerrar</button>
</div>

<!-- OVERLAY -->
<div id="overlay-filtros" onclick="toggleFiltros()" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 999;"></div>

<script> //SCRIPT DE FILTRADO
function toggleFiltros() {
    const panel = document.getElementById('panel-filtros');
    const overlay = document.getElementById('overlay-filtros');
    if (panel.style.left === "0px") {
        panel.style.left = "-300px";
        overlay.style.display = "none";
    } else {
        panel.style.left = "0px";
        overlay.style.display = "block";
    }
}

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
}

// Asignar evento al botón "Filtrar"
document.addEventListener('DOMContentLoaded', function() {
    const btnFiltros = document.getElementById('btn-filtros');
    if (btnFiltros) {
        btnFiltros.addEventListener('click', toggleFiltros);
    }
});
</script>
    
    <link rel="stylesheet" href="<?= base_url('styles/index.css') ?>">
</body>
</html>