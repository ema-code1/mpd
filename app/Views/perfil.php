<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Perfil' ?></title>
    <link rel="stylesheet" href="<?= base_url('styles/perfil.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <!-- Header -->
            <div class="profile-header">
                <h1 class="profile-title">Mi Perfil</h1>
                <p class="profile-subtitle">Gestiona tu información personal</p>
            </div>

            <!-- Foto de Perfil -->
            <div class="profile-picture-section">
                <div class="picture-container">
                    <div class="picture-wrapper">
                        <img id="profileImage" src="<?= $user['foto_perfil'] ? base_url('ppimages/' . $user['foto_perfil']) : base_url('assets/default-avatar.png') ?>" 
                             alt="Foto de perfil" class="profile-image">
                        <div class="picture-overlay" id="pictureOverlay">
                            <i class="ri-camera-line"></i>
                            <span>Cambiar foto</span>
                        </div>
                    </div>
                    <input type="file" id="fotoInput" accept="image/*" style="display: none;">
                    <div class="picture-info">
                        <p>Formatos: JPG, PNG, GIF, WebP</p>
                        <p>Máximo: 5MB</p>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form action="<?= base_url('perfil/actualizar') ?>" method="POST" class="profile-form">
                <?php if (session('success')): ?>
                    <div class="alert alert-success">
                        <i class="ri-checkbox-circle-line"></i>
                        <?= session('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session('errors')): ?>
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line"></i>
                        <div class="alert-content">
                            <?php foreach (session('errors') as $error): ?>
                                <p><?= $error ?></p>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre completo</label>
                        <input type="text" id="name" name="name" class="form-input"
                               value="<?= old('name', $user['name'] ?? '') ?>" 
                               placeholder="Ingresa tu nombre completo" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-input"
                               value="<?= old('email', $user['email'] ?? '') ?>" 
                               placeholder="Ingresa tu correo electrónico" required>
                    </div>
                </div>

                <div class="form-divider"></div>

                <div class="form-group full-width">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Dejar en blanco para no cambiar">
                </div>

                <div class="form-group full-width">
                    <label for="password_confirm" class="form-label">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                           placeholder="Confirma la nueva contraseña">
                </div>

                <div class="form-buttons">
                    <a href="<?= base_url('panel') ?>" class="btn btn-cancel">
                        <i class="ri-arrow-left-line"></i>
                        Volver
                    </a>
                    <button type="submit" class="btn btn-submit">
                        <i class="ri-save-line"></i>
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fotoInput = document.getElementById('fotoInput');
            const profileImage = document.getElementById('profileImage');
            const pictureOverlay = document.getElementById('pictureOverlay');

            // Abrir selector de archivos
            pictureOverlay.addEventListener('click', function(e) {
                e.stopPropagation();
                fotoInput.click();
            });

            // Mostrar overlay al hacer hover
            const pictureWrapper = pictureOverlay.parentElement;
            pictureWrapper.addEventListener('mouseenter', function() {
                pictureOverlay.style.opacity = '1';
            });
            
            pictureWrapper.addEventListener('mouseleave', function() {
                pictureOverlay.style.opacity = '0';
            });

            // Manejar cambio de imagen
            fotoInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const file = e.target.files[0];
                    
                    // Validar tamaño (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        showPopup('Error', 'La imagen no debe superar los 5MB', 'error');
                        return;
                    }

                    // Validar tipo
                    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        showPopup('Error', 'Solo se permiten imágenes JPEG, PNG, GIF o WebP', 'error');
                        return;
                    }

                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    // Subir archivo
                    uploadPhoto(file);
                }
            });

            function uploadPhoto(file) {
                const formData = new FormData();
                formData.append('foto_perfil', file);

                fetch('<?= base_url('perfil/uploadFoto') ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.image_url) {
                            profileImage.src = data.image_url;
                        }
                        showPopup('Éxito', data.message, 'success');
                    } else {
                        showPopup('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Error', 'Error al subir la imagen', 'error');
                });
            }

            function showPopup(title, message, type) {
                // Crear popup
                const popup = document.createElement('div');
                popup.className = 'popup-overlay';
                popup.innerHTML = `
                    <div class="popup-container">
                        <div class="popup-icon ${type}">
                            <i class="ri-${type === 'success' ? 'check' : 'error-warning'}-line"></i>
                        </div>
                        <h3 class="popup-title">${title}</h3>
                        <p class="popup-message">${message}</p>
                        <button class="popup-button" onclick="this.closest('.popup-overlay').remove()">
                            Aceptar
                        </button>
                    </div>
                `;
                document.body.appendChild(popup);
            }
        });
    </script>
</body>
</html>