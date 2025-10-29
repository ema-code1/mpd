<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Nuevo Libro</title>
    <link href="<?= base_url('styles/upload_book.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <a href="#"><i class="ti ti-transfer"></i> Movimientos</a>
  </div>
    <div class="form-container">
        <h1 class="form-title">Cargar Nuevo Libro</h1>
        
        <form method="post" action="<?= site_url('crearLibro') ?>" enctype="multipart/form-data" id="bookForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="titulo" class="form-label">Título *</label>
                    <input type="text" id="titulo" name="titulo" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="autor" class="form-label">Autor *</label>
                    <input type="text" id="autor" name="autor" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-textarea" rows="4"></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="edicion" class="form-label">Edición</label>
                    <input type="text" id="edicion" name="edicion" class="form-input" placeholder="Ej: Tapa dura, De bolsillo">
                </div>
                
                <div class="form-group">
                    <label for="precio" class="form-label">Precio *</label>
                    <input type="number" id="precio" name="precio" class="form-input" step="0.01" min="0" required>
                </div>
            </div>

            <div class="form-group">
                <label for="categoria" class="form-label">Categoría *</label>
                <input type="text" id="categoria" name="categoria" class="form-input" required>
            </div>

            <div class="form-divider"></div>

            <!-- NUEVO: Área de drag & drop -->
            <div class="drag-drop-container">
                <label class="form-label">Fotos del Libro</label>
                <div class="drag-drop-area" id="dragDropArea">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <div class="drag-drop-text">Arrastra y suelta archivos aquí o</div>
                    <div class="drag-drop-browse" id="browseButton">Buscar</div>
                    <div class="drag-drop-info">Sube hasta 2 fotos. Max. tamaño de imagen: 20MB</div>
                    <input type="file" id="fileInput" class="drag-drop-input" accept="image/*" multiple>
                    <!-- Después del drag-drop-container -->
            <input type="file" name="foto1" id="hiddenFoto1" hidden>
            <input type="file" name="foto2" id="hiddenFoto2" hidden>
            </div>
            
            
                
                <div class="file-list" id="fileList">
                    <!-- Los archivos seleccionados aparecerán aquí -->
                </div>
        </form>
        <div class="form-buttons">
            <a href="<?= site_url('admin') ?>" class="btn-cancel">Cancelar</a>
            <button type="submit" class="btn-submit">Guardar Libro</button>
        </div>
    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dragDropArea = document.getElementById('dragDropArea');
        const browseButton = document.getElementById('browseButton');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const hiddenInput1 = document.getElementById('foto1');
        const hiddenInput2 = document.getElementById('foto2');
        let uploadedFiles = [];
        let fileCounter = 0;

        // SOLUCIÓN: Configurar el input file para selección múltiple
        fileInput.setAttribute('multiple', 'multiple');

        // SOLUCIÓN: Solo el botón de "Buscar" abre el file explorer
        browseButton.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            fileInput.click();
        });

        // SOLUCIÓN: Remover cualquier evento click del área de drop
        // Esto evita que interfiera con la selección múltiple

        // Configurar eventos de drag and drop CORRECTAMENTE
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
    document.getElementById('hiddenFoto1').files = dt1.files;
    document.getElementById('hiddenFoto2').files = dt2.files;
}

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        }
    });

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
</script>

</body>
</html>