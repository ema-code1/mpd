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
    <!-- Contenedor para popups -->
    <div id="popupOverlay" class="popup-overlay">
        <div id="popup" class="popup">
            <div id="popupIcon" class="popup-icon"></div>
            <h3 id="popupTitle" class="popup-title"></h3>
            <p id="popupMessage" class="popup-message"></p>
            <div class="popup-buttons">
                <button id="popupConfirm" class="popup-btn confirm">Aceptar</button>
                <button id="popupCancel" class="popup-btn cancel" style="display: none;">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Contenedor para notificaciones toast -->
    <div class="toast-container" id="toastContainer"></div>

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
                                <i class="ti ti-dots-vertical"></i>
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
    </div>

    <!-- Sección de Reseñas -->
<div class="reseñas-container">
    <div class="reseñas-header">
        <h2>Reseñas del Libro</h2>
        
        <!-- Estadísticas -->
        <div class="card">
            <div class="stats-wrapper">
                <p class="heading">Rating</p>
                <div class="bottom-wrapper">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="star">
                        <g data-name="Layer 2">
                            <g data-name="star">
                                <rect opacity="0" transform="rotate(90 12 12)" height="24" width="24"></rect>
                                <path d="M17.56 21a1 1 0 0 1-.46-.11L12 18.22l-5.1 2.67a1 1 0 0 1-1.45-1.06l1-5.63-4.12-4a1 1 0 0 1-.25-1 1 1 0 0 1 .81-.68l5.7-.83 2.51-5.13a1 1 0 0 1 1.8 0l2.54 5.12 5.7.83a1 1 0 0 1 .81.68 1 1 0 0 1-.25 1l-4.12 4 1 5.63a1 1 0 0 1-.4 1 1 1 0 0 1-.62.18z"></path>
                            </g>
                        </g>
                    </svg>
                    <p class="count"><?= $stats_resenas['promedio'] ?: '0.0' ?></p>
                </div>
            </div>
            <div class="stats-wrapper">
                <p class="heading">Reseñas</p>
                <div class="bottom-wrapper">
                    <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" class="thumb">
                        <path d="M472.06 334l-144.16-6.13c-4.61-.36-23.9-1.21-23.9-25.87 0-23.81 19.16-25.33 24.14-25.88L472.06 270c12.67.13 23.94 14.43 23.94 32s-11.27 31.87-23.94 32zM330.61 202.33L437.35 194C450 194 464 210.68 464 227.88v.33c0 16.32-11.14 29.62-24.88 29.79l-108.45-1.73C304 253 304 236.83 304 229.88c0-22.88 21.8-27.15 26.61-27.55zM421.85 480l-89.37-8.93C308 470.14 304 453.82 304 443.59c0-18.38 13.41-24.6 26.67-24.6l91-3c14.54.23 26.32 14.5 26.32 32s-11.67 31.67-26.14 32.01zm34.36-71.5l-126.4-6.21c-9.39-.63-25.81-3-25.81-26.37 0-12 4.35-25.61 25-27.53l127.19-3.88c13.16.14 23.81 13.49 23.81 31.4s-10.65 32.43-23.79 32.58z"></path>
                        <path fill="none" d="M133.55 238.06A15.85 15.85 0 01126 240a15.82 15.82 0 007.51-1.92zM174.14 168.78l.13-.23-.13.23c-20.5 35.51-30.36 54.95-33.82 62 3.47-7.07 13.34-26.51 33.82-62z"></path>
                        <path d="M139.34 232.84l1-2a16.27 16.27 0 01-6.77 7.25 16.35 16.35 0 005.77-5.25z"></path>
                        <path d="M316.06 52.62C306.63 39.32 291 32 272 32a16 16 0 00-14.31 8.84c-3 6.07-15.25 24-28.19 42.91-18 26.33-40.35 59.07-55.23 84.8l-.13.23c-20.48 35.49-30.35 54.93-33.82 62l-1 2a16.35 16.35 0 01-5.79 5.22 15.82 15.82 0 01-7.53 2h-25.31A84.69 84.69 0 0016 324.69v38.61a84.69 84.69 0 0084.69 84.7h48.79a17.55 17.55 0 019.58 2.89C182 465.87 225.34 480 272 480c7.45 0 14.19-.14 20.27-.38a8 8 0 006.2-12.68l-.1-.14C289.8 454.41 288 441 288 432a61.2 61.2 0 015.19-24.77 17.36 17.36 0 000-14.05 63.81 63.81 0 010-50.39 17.32 17.32 0 000-14 62.15 62.15 0 010-49.59 18.13 18.13 0 000-14.68A60.33 60.33 0 01288 239c0-8.2 2-21.3 8-31.19a15.63 15.63 0 001.14-13.64c-.38-1-.76-2.07-1.13-3.17a24.84 24.84 0 01-.86-11.58c3-19.34 9.67-36.29 16.74-54.16 3.08-7.78 6.27-15.82 9.22-24.26 6.14-17.57 4.3-35.2-5.05-48.38z"></path>
                    </svg>
                    <p class="count"><?= $stats_resenas['total'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario para agregar reseña (solo para usuarios logueados que no hayan reseñado) -->
    <?php if (session()->get('isLoggedIn') && !$user_ya_reseno): ?>
    <div class="agregar-resena-form">
        <h3>Escribe tu reseña</h3>
        <form id="formResena" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="libro_id" value="<?= $libro['id'] ?>">
            
            <div class="rating-container">
                <label>Calificación:</label>
                <!-- Formulario de reseña -->
                <div class="rating mb-3">
                <input value="5" name="rating" id="star5" type="radio">
                <label for="star5"></label>
                <input value="4" name="rating" id="star4" type="radio">
                <label for="star4"></label>
                <input value="3" name="rating" id="star3" type="radio">
                <label for="star3"></label>
                <input value="2" name="rating" id="star2" type="radio">
                <label for="star2"></label>
                <input value="1" name="rating" id="star1" type="radio">
                <label for="star1"></label>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion">Contanos tu experiencia:</label>
                <textarea name="descripcion" id="descripcion" rows="4" placeholder="Escribe tu reseña aquí..." required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Publicar Reseña</button>
                <button type="button" class="btn btn-secondary" onclick="resetForm()">Cancelar</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Lista de reseñas existentes -->
<div class="reseñas-list">
    <?php if (!empty($resenas)): ?>
        <?php foreach ($resenas as $resena): ?>
        <div class="reseña-card" id="resena-<?= $resena['id'] ?>">
            <div class="reseña-header">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php if (!empty($resena['user_foto'])): ?>
                            <img src="<?= base_url('ppimages/' . $resena['user_foto']) ?>" alt="<?= esc($resena['user_name']) ?>" class="avatar-img">
                        <?php else: ?>
                            <div class="avatar-text"><?= strtoupper(substr($resena['user_name'], 0, 2)) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?= esc($resena['user_name']) ?></div>
                        <div class="reseña-date">
                            <?= date('d/m/Y', strtotime($resena['created_at'])) ?>
                            <?php if (!empty($resena['updated_at']) && $resena['updated_at'] != $resena['created_at']): ?>
                                <span class="editado-badge">• Editado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="reseña-actions">
                    <div class="reseña-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $resena['rating'] ? 'filled' : '' ?>">★</span>
                        <?php endfor; ?>
                    </div>

                    <?php if (session()->get('isLoggedIn') && session()->get('userId') == $resena['user_id']): ?>
                    <div class="card-options">
                        <button class="options-btn" title="Opciones de reseña">
                            <i class="ti ti-dots-vertical"></i>
                        </button>
                        <div class="options-menu">
                            <a href="#" class="option-item" onclick="editarResena(<?= $resena['id'] ?>)">
                                <i class="ti ti-edit"></i>
                                <span>Editar</span>
                            </a>
                            <a href="#" class="option-item delete-option" onclick="eliminarResena(<?= $resena['id'] ?>)">
                                <i class="ti ti-trash"></i>
                                <span>Eliminar</span>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="reseña-content">
                <p id="resena-content-<?= $resena['id'] ?>"><?= nl2br(esc($resena['descripcion'])) ?></p>
            </div>
            
            <!-- Formulario de edición (oculto inicialmente) -->
<div class="editar-resena-form" id="editar-form-<?= $resena['id'] ?>" style="display: none;">
    <form id="formEditarResena-<?= $resena['id'] ?>" method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="resena_id" value="<?= $resena['id'] ?>">
        
        <div class="rating-container">
            <label>Calificación:</label>
            <div class="rating mb-3">
                <input value="5" name="rating" id="edit-star5-<?= $resena['id'] ?>" type="radio" <?= $resena['rating'] == 5 ? 'checked' : '' ?>>
                <label for="edit-star5-<?= $resena['id'] ?>"></label>
                <input value="4" name="rating" id="edit-star4-<?= $resena['id'] ?>" type="radio" <?= $resena['rating'] == 4 ? 'checked' : '' ?>>
                <label for="edit-star4-<?= $resena['id'] ?>"></label>
                <input value="3" name="rating" id="edit-star3-<?= $resena['id'] ?>" type="radio" <?= $resena['rating'] == 3 ? 'checked' : '' ?>>
                <label for="edit-star3-<?= $resena['id'] ?>"></label>
                <input value="2" name="rating" id="edit-star2-<?= $resena['id'] ?>" type="radio" <?= $resena['rating'] == 2 ? 'checked' : '' ?>>
                <label for="edit-star2-<?= $resena['id'] ?>"></label>
                <input value="1" name="rating" id="edit-star1-<?= $resena['id'] ?>" type="radio" <?= $resena['rating'] == 1 ? 'checked' : '' ?>>
                <label for="edit-star1-<?= $resena['id'] ?>"></label>
            </div>
        </div>

        <div class="form-group">
            <label for="edit-descripcion-<?= $resena['id'] ?>">Reseña:</label>
            <textarea name="descripcion" id="edit-descripcion-<?= $resena['id'] ?>" class="form-control" rows="3" required><?= htmlspecialchars($resena['descripcion']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="button" onclick="cancelarEdicion(<?= $resena['id'] ?>)" class="btn btn-secondary">Cancelar</button>
            <button type="submit" class="btn btn-primary">Actualizar Reseña</button>
        </div>
    </form>
</div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="no-resenas">
            <p>Este libro aún no tiene reseñas. ¡Sé el primero en opinar!</p>
        </div>
    <?php endif; ?>
</div>
<a href="<?= site_url('/') ?>" class="btn-back">
    <i class="ti ti-arrow-left"></i> Volver al catálogo
</a>


<script>
// JavaScript para manejar las reseñas
let formChanged = false;

// Funciones para mostrar popups y notificaciones
function showPopup(title, message, type = 'info', confirmCallback = null) {
    const overlay = document.getElementById('popupOverlay');
    const popup = document.getElementById('popup');
    const icon = document.getElementById('popupIcon');
    const popupTitle = document.getElementById('popupTitle');
    const popupMessage = document.getElementById('popupMessage');
    const confirmBtn = document.getElementById('popupConfirm');

    popup.className = 'popup ' + type;
    
    switch(type) {
        case 'success': icon.innerHTML = '<i class="ti ti-circle-check"></i>'; break;
        case 'error': icon.innerHTML = '<i class="ti ti-circle-x"></i>'; break;
        case 'warning': icon.innerHTML = '<i class="ti ti-alert-triangle"></i>'; break;
        default: icon.innerHTML = '<i class="ti ti-info-circle"></i>';
    }

    popupTitle.textContent = title;
    popupMessage.textContent = message;

    confirmBtn.onclick = function() {
        hidePopup();
        if (confirmCallback) confirmCallback();
    };

    overlay.classList.add('active');
}

function hidePopup() {
    document.getElementById('popupOverlay').classList.remove('active');
}

function showNotification(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    let icon = '';
    switch(type) {
        case 'success': icon = '<i class="ti ti-circle-check toast-icon"></i>'; break;
        case 'error': icon = '<i class="ti ti-circle-x toast-icon"></i>'; break;
        case 'warning': icon = '<i class="ti ti-alert-triangle toast-icon"></i>'; break;
        default: icon = '<i class="ti ti-info-circle toast-icon"></i>';
    }
    
    toast.innerHTML = `${icon}<span class="toast-message">${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Función para cambiar imagen principal
function changeImage(src) {
    const mainImage = document.getElementById('mainImage');
    document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
    event.currentTarget.classList.add('active');
    
    mainImage.style.opacity = '0';
    setTimeout(() => {
        mainImage.src = src;
        mainImage.style.opacity = '1';
    }, 200);
}

// Función para agregar al carrito
function addToCart(libroId) {
    fetch('<?= site_url("cart/add/") ?>' + libroId, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `<?= csrf_token() ?>=<?= csrf_hash() ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) showNotification('¡Libro añadido al carrito!', 'success');
        else showNotification('Error al añadir el libro', 'error');
    })
    .catch(() => showNotification('Error de conexión', 'error'));
}

// Funciones para manejar reseñas
function resetForm() {
    if (formChanged) {
        showPopup('¿Cancelar reseña?', 'Tienes cambios sin guardar', 'warning', () => {
            document.getElementById('formResena').reset();
            formChanged = false;
        });
    } else {
        document.getElementById('formResena').reset();
    }
}

function editarResena(resenaId) {
    // Guardar posición actual del scroll
    const scrollPosition = window.scrollY;
    
    document.getElementById(`resena-content-${resenaId}`).style.display = 'none';
    document.getElementById(`editar-form-${resenaId}`).style.display = 'block';
    
    // Restaurar posición del scroll después de un pequeño delay
    setTimeout(() => {
        window.scrollTo(0, scrollPosition);
    }, 10);
}

function cancelarEdicion(resenaId) {
    // Guardar posición actual del scroll
    const scrollPosition = window.scrollY;
    
    if (formChanged) {
        showPopup('¿Descartar cambios?', 'Tienes cambios sin guardar', 'warning', () => {
            document.getElementById(`editar-form-${resenaId}`).style.display = 'none';
            document.getElementById(`resena-content-${resenaId}`).style.display = 'block';
            formChanged = false;
            // Restaurar posición del scroll
            setTimeout(() => {
                window.scrollTo(0, scrollPosition);
            }, 10);
        });
    } else {
        document.getElementById(`editar-form-${resenaId}`).style.display = 'none';
        document.getElementById(`resena-content-${resenaId}`).style.display = 'block';
        // Restaurar posición del scroll
        setTimeout(() => {
            window.scrollTo(0, scrollPosition);
        }, 10);
    }
}

// Función para actualizar reseña - CORREGIDA CON RUTA
function actualizarResena(resenaId) {
    const form = document.getElementById(`formEditarResena-${resenaId}`);
    
    // DEBUG: Verificar que el formulario existe
    console.log('Buscando formulario con ID:', `formEditarResena-${resenaId}`);
    console.log('Formulario encontrado:', form);
    
    if (!form) {
        console.error('Formulario no encontrado para reseña:', resenaId);
        showNotification('Error: No se pudo encontrar el formulario', 'error');
        return;
    }
    
    const formData = new FormData(form);
    
    // DEBUG: Verificar datos
    console.log('Actualizando reseña:', resenaId);
    console.log('Datos del formulario:', {
        rating: formData.get('rating'),
        descripcion: formData.get('descripcion'),
        resena_id: formData.get('resena_id')
    });
    
    // USAR site_url() de CodeIgniter para la ruta correcta
    fetch(`<?= site_url('libro/actualizarResena/') ?>${resenaId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Respuesta HTTP:', response.status, response.statusText);
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.success) {
            showNotification('¡Reseña actualizada!', 'success');
            formChanged = false;
            // Recargar después de un tiempo
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            const errorMsg = data.errors ? Object.values(data.errors).join(', ') : 'Error al actualizar la reseña';
            showNotification(errorMsg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error de conexión: ' + error.message, 'error');
    });
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Formulario nueva reseña
    const formResena = document.getElementById('formResena');
    if (formResena) {
        formResena.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('<?= site_url("libro/agregarResena") ?>', {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('¡Reseña publicada!', 'success');
                    formChanged = false;
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.errors ? Object.values(data.errors).join(', ') : 'Error al publicar', 'error');
                }
            })
            .catch(() => showNotification('Error de conexión', 'error'));
        });

        ['change', 'input'].forEach(event => {
            formResena.addEventListener(event, () => formChanged = true);
        });
    }
    
// Formularios edición reseñas - CORREGIDO
document.querySelectorAll('form[id^="formEditarResena-"]').forEach(form => {
    console.log('Configurando formulario:', form.id);
    
    // Extraer el ID de la reseña del ID del formulario
    const formId = form.id;
    const resenaId = formId.replace('formEditarResena-', '');
    console.log('ID de reseña extraído:', resenaId);
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Enviando formulario de edición para reseña:', resenaId);
        actualizarResena(resenaId);
    });
    
    // Detectar cambios en formularios de edición
    form.addEventListener('input', function() {
        formChanged = true;
        console.log('Formulario cambiado');
    });
});

    // Dropdowns con transición
    document.querySelectorAll('.options-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const menu = this.nextElementSibling;
            const isShowing = menu.classList.contains('show');

            document.querySelectorAll('.options-menu.show').forEach(m => m.classList.remove('show'));
            if (!isShowing) menu.classList.add('show');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.options-menu.show').forEach(menu => menu.classList.remove('show'));
    });

    // Cerrar popup al hacer clic fuera
    document.getElementById('popupOverlay').addEventListener('click', function(e) {
        if (e.target === this) hidePopup();
    });

    // Advertencia cambios sin guardar
    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'Tienes cambios sin guardar';
        }
    });
});

// Función para mantener la posición al recargar
function saveScrollPosition() {
    sessionStorage.setItem('scrollPosition', window.scrollY);
}

function restoreScrollPosition() {
    const scrollPosition = sessionStorage.getItem('scrollPosition');
    if (scrollPosition) {
        window.scrollTo(0, parseInt(scrollPosition));
        sessionStorage.removeItem('scrollPosition');
    }
}

// Llamar esta función cuando la página cargue
document.addEventListener('DOMContentLoaded', function() {
    restoreScrollPosition();
    
    // Guardar posición antes de enviar formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', saveScrollPosition);
    });
});

function eliminarResena(resenaId) {
    showPopup('¿Eliminar reseña?', 'Esta acción no se puede deshacer', 'warning', () => {
        console.log('Eliminando reseña:', resenaId);
        
        // Crear FormData para incluir el CSRF token
        const formData = new FormData();
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        // USAR site_url() de CodeIgniter para la ruta correcta
        fetch(`<?= site_url('libro/eliminarResena/') ?>${resenaId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Respuesta HTTP eliminar:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Respuesta eliminar:', data);
            if (data.success) {
                showNotification('Reseña eliminada exitosamente', 'success');
                // Eliminar la tarjeta de reseña del DOM
                const resenaElement = document.getElementById(`resena-${resenaId}`);
                if (resenaElement) {
                    resenaElement.remove();
                }
                // Recargar estadísticas después de un tiempo
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                const errorMsg = data.errors ? Object.values(data.errors).join(', ') : 'Error al eliminar la reseña';
                showNotification(errorMsg, 'error');
            }
        })
        .catch(error => {
            console.error('Error al eliminar:', error);
            showNotification('Error de conexión: ' + error.message, 'error');
        });
    });
}
</script>
</body>
</html>