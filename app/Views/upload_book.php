<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Libro</title>
    <link href="<?= base_url('styles/uploadbook.css') ?>" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h2 class="text-center" style="color: #EF8D00;">Cargar Nuevo Libro</h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('crearLibro') ?>" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="titulo" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="autor" class="form-label">Autor *</label>
                                    <input type="text" class="form-control" id="autor" name="autor" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edicion" class="form-label">Edición</label>
                                    <input type="text" class="form-control" id="edicion" name="edicion" placeholder="Ej: Tapa dura, De bolsillo">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="precio" class="form-label">Precio *</label>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría *</label>
                                <input type="text" class="form-control" id="categoria" name="categoria" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="foto1" class="form-label">Foto 1</label>
                                    <input type="file" class="form-control" id="foto1" name="foto1" accept="image/*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="foto2" class="form-label">Foto 2</label>
                                    <input type="file" class="form-control" id="foto2" name="foto2" accept="image/*">
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-custom">Guardar Libro</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>