<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($libro['titulo']) ?> - Editorial MPD</title>
    <link rel="stylesheet" href="<?= base_url('styles/book_details.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
</head>

<body>
    <!-- Comentario -->
    <div class="book-details-container">
        <div class="book-details">
            <!-- Sección de imágenes con slider -->
            <div class="book-images">
                <div class="main-image-container">
                    <?php if (!empty($libro['foto1'])): ?>
                        <img src="<?= base_url($libro['foto1']) ?>" alt="<?= esc($libro['titulo']) ?>" class="main-image active" id="mainImage">
                    <?php else: ?>
                        <img src="<?= base_url('imgs/noimageavailable.jpg') ?>" alt="Libro sin imagen" class="main-image active" id="mainImage">
                    <?php endif; ?>
                </div>

                <?php if (!empty($libro['foto2'])): ?>
                    <div class="thumbnail-container">
                        <div class="thumbnail <?= empty($libro['foto2']) ? 'disabled' : '' ?>" onclick="changeImage('<?= base_url($libro['foto1']) ?>')">
                            <img src="<?= base_url($libro['foto1']) ?>" alt="Miniatura 1">
                        </div>
                        <div class="thumbnail <?= empty($libro['foto2']) ? 'disabled' : '' ?>" onclick="changeImage('<?= base_url($libro['foto2']) ?>')">
                            <img src="<?= base_url($libro['foto2']) ?>" alt="Miniatura 2">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Sección de información del libro -->
            <div class="book-info">
                <div class="title-header">
                    <h1 class="book-title"><?= esc($libro['titulo']) ?></h1>

                    <!-- Botón de opciones (SOLO para ADMIN) -->
                    <?php if (session()->get('isLoggedIn') && session()->get('role') === 'administrador'): ?>
                        <div class="card-options">
                            <button class="options-btn" title="Más opciones">
                                <i class="ti ti-pencil"></i>
                            </button>
                            <div class="options-menu">
                                <a href="<?= site_url('libro/editar/' . $libro['id']) ?>" class="option-item">
                                    <i class="ti ti-edit"></i>
                                    <span>Editar publicación</span>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <p class="book-price">$<?= number_format($libro['precio'], 2) ?></p>

                <div class="book-meta">
                    <div class="meta-item">
                        <i class="ti ti-user"></i>
                        <span><strong>Autor:</strong> <?= esc($libro['autor']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="ti ti-book"></i>
                        <span><strong>Edición:</strong> <?= esc($libro['edicion'] ?? 'No especificada') ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="ti ti-category"></i>
                        <span><strong>Categoría:</strong> <?= esc($libro['categoria']) ?></span>
                    </div>
                </div>

                <div class="book-description">
                    <h3><i class="ti ti-align-left"></i> Descripción</h3>
                    <p><?= nl2br(esc($libro['descripcion'])) ?></p>
                </div>

                <div class="book-actions">
                    <button class="btn btn-buy">
                        Comprar ahora
                    </button>

                    <?php if (session()->get('isLoggedIn') && session()->get('role') === 'comprador'): ?>
                    <button type="button" class="btn btn-wishlist" onclick="addToCart(<?= $libro['id'] ?>)">
                        <i class="ti ti-shopping-cart-plus"></i> Agregar al carrito
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <a href="<?= site_url('/') ?>" class="btn-back">
            <i class="ti ti-arrow-left"></i> Volver al catálogo
        </a>
    </div>

    <script>
        function changeImage(imageSrc) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = imageSrc;

            // Remover clase active de todas las miniaturas
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
            });

            // Agregar clase active a la miniatura clickeada
            event.currentTarget.classList.add('active');
        }


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
    <script> // Para carrito
        function addToCart(libroId) {
            fetch('<?= site_url('cart/add/') ?>' + libroId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('¡Libro añadido al carrito!');
                    } else {
                        alert('Error al añadir el libro.');
                    }
                });
        }
    </script>

    <style> 
    /* para el carrito */
        .btn-add-to-cart {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: bold;
            margin-top: 1rem;
            transition: var(--transition);
        }

        .btn-add-to-cart:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }
    </style>
</body>

</html>