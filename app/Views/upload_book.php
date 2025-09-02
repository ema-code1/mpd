<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Nuevo Libro</title>
    <link href="<?= base_url('styles/upload_book.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h1 class="form-title">Cargar Nuevo Libro</h1>
        
        <form method="post" action="<?= site_url('crearLibro') ?>" enctype="multipart/form-data">
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

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Foto 1</label>
                    <div class="file-input-container">
                        <input type="file" id="foto1" name="foto1" class="file-input" accept="image/*">
                        <label for="foto1" class="file-label">Seleccionar archivo</label>
                        <span class="file-status">Ningún archivo seleccionado</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Foto 2</label>
                    <div class="file-input-container">
                        <input type="file" id="foto2" name="foto2" class="file-input" accept="image/*">
                        <label for="foto2" class="file-label">Seleccionar archivo</label>
                        <span class="file-status">Ningún archivo seleccionado</span>
                    </div>
                </div>
            </div>

            <div class="form-divider"></div>

            <div class="form-buttons">
                <a href="<?= site_url('volver_home') ?>" class="btn-cancel">Cancelar</a>
                <button type="submit" class="btn-submit">Guardar Libro</button>
            </div>
        </form>
    </div>

    <script>
        // JavaScript para mostrar el nombre del archivo seleccionado
        document.querySelectorAll('.file-input').forEach(input => {
            input.addEventListener('change', function() {
                const status = this.parentElement.querySelector('.file-status');
                status.textContent = this.files.length > 0 ? this.files[0].name : 'Ningún archivo seleccionado';
            });
        });
    </script>
</body>
</html>