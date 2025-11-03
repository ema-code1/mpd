<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro - MPD</title>
    <link href="<?= base_url('styles/edit_delete_book.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
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
  
    <div class="form-container">
        <h1 class="form-title">Editar Publicación</h1>
        
        <form method="post" action="<?= site_url('libro/actualizar/' . $libro['id']) ?>" enctype="multipart/form-data" id="bookForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input" value="<?= htmlspecialchars($libro['titulo'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="autor" class="form-label">Autor *</label>
                    <input type="text" id="autor" name="autor" class="form-input" value="<?= htmlspecialchars($libro['autor'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-textarea" rows="4"><?= htmlspecialchars($libro['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="edicion" class="form-label">Edición</label>
                    <input type="text" id="edicion" name="edicion" class="form-input" placeholder="Ej: Tapa dura, De bolsillo" value="<?= htmlspecialchars($libro['edicion'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="precio" class="form-label">Precio *</label>
                    <input type="number" id="precio" name="precio" class="form-input" step="0.01" min="0" value="<?= htmlspecialchars($libro['precio'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="categoria" class="form-label">Categoría *</label>
                <input type="text" id="categoria" name="categoria" class="form-input" value="<?= htmlspecialchars($libro['categoria'] ?? '') ?>" required>
            </div>

            <div class="form-divider"></div>

            <!-- Área de drag & drop para imágenes -->
            <div class="drag-drop-container">
                <label class="form-label">Fotos del Libro</label>
                <div class="drag-drop-area" id="dragDropArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div class="drag-drop-text">Arrastra y suelta archivos aquí o</div>
                    <div class="drag-drop-browse" id="browseButton">Buscar</div>
                    <div class="drag-drop-info">Sube hasta 2 fotos. Max. tamaño de imagen: 20MB</div>
                    <input type="file" id="fileInput" class="drag-drop-input" accept="image/*" multiple>
                </div>
                <!-- Inputs para nuevas fotos -->
                <input type="file" name="foto1" id="hiddenfoto1" hidden>
                <input type="file" name="foto2" id="hiddenfoto2" hidden>
                
                <!-- Mostrar imágenes actuales -->
                <div class="current-images">
    <h3>Imágenes actuales:</h3>
    <div class="image-previews">
        <?php if (!empty($libro['foto1']) || !empty($libro['foto2'])): ?>
            <?php if (!empty($libro['foto1'])): ?>
                <div class="image-preview">
                    <img src="<?= base_url($libro['foto1']) ?>" alt="Imagen 1 del libro">
                    <button type="button" class="delete-image" onclick="eliminarImagen(0)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($libro['foto2'])): ?>
                <div class="image-preview">
                    <img src="<?= base_url($libro['foto2']) ?>" alt="Imagen 2 del libro">
                    <button type="button" class="delete-image" onclick="eliminarImagen(1)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p>No hay imágenes cargadas para este libro.</p>
        <?php endif; ?>
    </div>
</div>
                
                <div class="file-list" id="fileList">
                    <!-- Los archivos seleccionados aparecerán aquí -->
                </div>
            </div>

            <div class="form-buttons">
                <button type="button" class="btn-delete" onclick="confirmDelete('<?= base_url('libros/delete/' . $libro['id']) ?>')">Borrar Libro</button>
                <div>
                    <a href="<?= site_url('libro/' . $libro['id']) ?>" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit">Guardar Cambios</button>
                </div>
            </div>
        </form>
        
        <!-- Formulario oculto para eliminar -->
        <form id="deleteForm" action="<?= site_url('libro/eliminar/' . $libro['id']) ?>" method="POST" style="display: none;">
            <input type="hidden" name="_method" value="DELETE">
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const dragDropArea = document.getElementById('dragDropArea');
        const browseButton = document.getElementById('browseButton');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        let uploadedFiles = [];
        let fileCounter = 0;

        // Configurar el input file para selección múltiple
        fileInput.setAttribute('multiple', 'multiple');

        // Solo el botón de "Buscar" abre el file explorer
        browseButton.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            fileInput.click();
        });

        // Configurar eventos de drag and drop
        dragDropArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('dragover');
        });

        dragDropArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
        });

        dragDropArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                processFiles(files);
            }
        });

        // Manejar selección de archivos desde el input
        fileInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                processFiles(files);
            }
            // Resetear el input
            this.value = '';
        });

        function processFiles(files) {
            // Convertir FileList a array
            const newFiles = Array.from(files);
            
            // Verificar límite de archivos
            if (uploadedFiles.length + newFiles.length > 2) {
                alert('Solo puedes subir hasta 2 fotos');
                return;
            }

            newFiles.forEach(file => {
                // Validaciones
                if (file.size > 20 * 1024 * 1024) {
                    alert(`El archivo ${file.name} excede el tamaño máximo de 20MB`);
                    return;
                }

                if (!file.type.startsWith('image/')) {
                    alert(`El archivo ${file.name} no es una imagen válida`);
                    return;
                }

                // Agregar archivo
                uploadedFiles.push(file);
                displayFile(file);
            });

            updateHiddenInputs();
        }

        function displayFile(file) {
            const fileId = `file-${fileCounter++}`;
            const fileItem = document.createElement('div');
            fileItem.className = 'file-item';
            fileItem.id = fileId;

            const fileSize = formatFileSize(file.size);
            
            fileItem.innerHTML = `
                <div class="file-icon">
                    <i class="fas fa-file-image"></i>
                </div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="file-size">${fileSize}</div>
                    <div class="file-progress">
                        <div class="progress-bar"></div>
                    </div>
                </div>
                <button type="button" class="file-remove" data-file-id="${fileId}" data-file-name="${file.name}">
                    <i class="fas fa-times"></i>
                </button>
            `;

            fileList.appendChild(fileItem);

            // Event listener para eliminar
            const removeButton = fileItem.querySelector('.file-remove');
            removeButton.addEventListener('click', function() {
                removeFile(fileId, this.getAttribute('data-file-name'));
            });

            // Simular progreso
            simulateUploadProgress(fileItem);
        }

        function simulateUploadProgress(fileItem) {
            const progressBar = fileItem.querySelector('.progress-bar');
            let width = 0;
            
            const interval = setInterval(() => {
                if (width >= 100) {
                    clearInterval(interval);
                    fileItem.querySelector('.file-progress').style.display = 'none';
                } else {
                    width += Math.random() * 20;
                    progressBar.style.width = Math.min(width, 100) + '%';
                }
            }, 100);
        }

        function removeFile(fileId, fileName) {
            // Remover de la vista
            const fileElement = document.getElementById(fileId);
            if (fileElement) {
                fileElement.remove();
            }

            // Remover del array
            uploadedFiles = uploadedFiles.filter(file => file.name !== fileName);
            
            // Actualizar inputs ocultos
            updateHiddenInputs();
        }

        function updateHiddenInputs() {
            // Crear nuevos DataTransfer objects
            const dt1 = new DataTransfer();
            const dt2 = new DataTransfer();

            // Agregar archivos a los DataTransfer objects
            if (uploadedFiles.length > 0) dt1.items.add(uploadedFiles[0]);
            if (uploadedFiles.length > 1) dt2.items.add(uploadedFiles[1]);

            // Asignar los files a los inputs ocultos
            document.getElementById('hiddenfoto1').files = dt1.files;
            document.getElementById('hiddenfoto2').files = dt2.files;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }
    });

    // FUNCIONES GLOBALES - Fuera del DOMContentLoaded
    function eliminarImagen(index) {
        console.log('Eliminando imagen índice:', index);
        
        // Ocultar la imagen inmediatamente
        const previews = document.querySelectorAll('.image-preview');
        if (previews[index]) {
            previews[index].style.display = 'none';
            console.log('Imagen ocultada');
        }
        
        // Crear input hidden para indicar al servidor qué imagen eliminar
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'eliminarImagenes[]';
        hiddenInput.value = index;
        
        // Agregar el input al formulario
        document.getElementById('bookForm').appendChild(hiddenInput);
        console.log('Input hidden creado con valor:', index);
    }

    function confirmDelete() {
        if (confirm('¿Estás seguro de que deseas eliminar este libro? Esta acción no se puede deshacer.')) {
            document.getElementById('deleteForm').submit();
        }
    }


// SOLO ESTO en el script
document.getElementById('bookForm').addEventListener('submit', function(e) {
    e.preventDefault();
    showSuccessPopup();
});

function showSuccessPopup() {
    const popup = document.getElementById('successPopup');
    popup.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function redirectToHome() {
    // Enviar formulario de forma tradicional
    const form = document.getElementById('bookForm');
    form.submit();
}

// ===============================
// POPUP BORRAR LIBRO
// ===============================
window.confirmDelete = function (deleteUrl) {
  const overlay = document.getElementById('deletePopupOverlay');
  const confirmBtn = document.getElementById('deleteConfirm');
  const cancelBtn = document.getElementById('deleteCancel');

  overlay.classList.add('active');

  confirmBtn.onclick = () => {
    window.location.href = deleteUrl; // redirige a tu ruta base_url('delete/id')
  };

  cancelBtn.onclick = () => overlay.classList.remove('active');

  overlay.onclick = (e) => {
    if (e.target === overlay) overlay.classList.remove('active');
  };
};

// ===============================
// POPUP CANCELAR EDICIÓN
// ===============================
document.addEventListener('DOMContentLoaded', function () {
  const form = document.querySelector('form'); // tu formulario principal
  const cancelBtn = document.querySelector('.btn-cancel');
  const overlay = document.getElementById('cancelPopupOverlay');
  const confirmExit = document.getElementById('cancelConfirm');
  const abortExit = document.getElementById('cancelAbort');

  let formChanged = false;

  // Detecta si el usuario cambió algo
  form.addEventListener('input', () => {
    formChanged = true;
  });

  cancelBtn.addEventListener('click', (e) => {
    if (formChanged) {
      e.preventDefault();
      overlay.classList.add('active');
    } // Si no cambió nada, deja que el link funcione normal
  });

  confirmExit.addEventListener('click', () => {
    window.location.href = cancelBtn.getAttribute('href');
  });

  abortExit.addEventListener('click', () => overlay.classList.remove('active'));

  overlay.addEventListener('click', (e) => {
    if (e.target === overlay) overlay.classList.remove('active');
  });
});

</script>
<div id="successPopup" class="popup-overlay" style="display: none;">
    <div class="popup-container">
        <div class="popup-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2 class="popup-title">¡Éxito!</h2>
        <p class="popup-message">Libro subido correctamente</p>
        <button class="popup-button" onclick="redirectToHome()">Ir al inicio</button>
    </div>
</div>
<!-- Popup para BORRAR -->
<div id="deletePopupOverlay" class="popup-overlay">
  <div class="popup error">
    <div class="popup-icon">🗑️</div>
    <h3 class="popup-title">¿Eliminar libro?</h3>
    <p class="popup-message">Esta acción no se puede deshacer. ¿Querés continuar?</p>
    <div class="popup-buttons">
      <button id="deleteConfirm" class="popup-btn delete">Sí, borrar</button>
      <button id="deleteCancel" class="popup-btn cancel">Cancelar</button>
    </div>
  </div>
</div>

<!-- Popup para CANCELAR -->
<div id="cancelPopupOverlay" class="popup-overlay">
  <div class="popup warning">
    <div class="popup-icon">⚠️</div>
    <h3 class="popup-title">¿Descartar cambios?</h3>
    <p class="popup-message">Perderás los cambios que hiciste si salís sin guardar.</p>
    <div class="popup-buttons">
      <button id="cancelConfirm" class="popup-btn confirm">Sí, salir</button>
      <button id="cancelAbort" class="popup-btn cancel">Volver</button>
    </div>
  </div>
</div>

</body>

</html>