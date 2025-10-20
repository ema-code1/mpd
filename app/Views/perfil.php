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
    <section>
  <div class="wave">
    <span></span>
    <span></span>
    <span></span>
  </div>
  <div class="profile-container">
        <div class="profile-card">
            <!-- Header -->
            <div class="profile-header">
                <h1 class="profile-title">Editar Perfil</h1>
            </div>

            <!-- Foto de Perfil -->
            <div class="profile-picture-section">
                <div class="picture-container">
                    <div class="picture-wrapper">
                        <img id="profileImage" src="<?= $user['foto_perfil'] ? base_url('ppimages/' . $user['foto_perfil']) : base_url('assets/default-avatar.png') ?>" 
                             alt="Foto de perfil" class="profile-image">
                        <div class="picture-overlay" id="pictureOverlay">
                            <i class="ri-camera-line"></i>
                        </div>
                    </div>
                    <input type="file" id="fotoInput" accept="image/*" style="display: none;">
                    <input type="hidden" id="fotoTemp" name="foto_temp">
                </div>
            </div>

            <!-- Formulario -->
            <form id="profileForm" action="<?= base_url('perfil/actualizar') ?>" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="<?= old('name', $user['name'] ?? '') ?>" 
                           placeholder="Tu nombre completo">
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?= old('email', $user['email'] ?? '') ?>" 
                           placeholder="tu@email.com">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Nueva Contraseña</label>
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="********">
                </div>

                <div class="form-group">
                    <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                           placeholder="********">
                </div>

                <div class="form-buttons">
                    <button type="button" id="cancelBtn" class="btn btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-submit" disabled>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup de advertencia -->
    <div id="warningPopup" class="popup-overlay" style="display: none;">
        <div class="popup-container">
            <div class="popup-icon warning">
                <i class="ri-error-warning-line"></i>
            </div>
            <h3 class="popup-title">Cambios sin guardar</h3>
            <p class="popup-message">Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?</p>
            <div class="popup-buttons">
                <button type="button" class="popup-button secondary" id="cancelLeave">
                    Seguir editando
                </button>
                <button type="button" class="popup-button primary" id="confirmLeave">
                    Salir sin guardar
                </button>
            </div>
        </div>
    </div>
</section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const cancelBtn = document.getElementById('cancelBtn');
            const submitBtn = document.getElementById('submitBtn');
            const warningPopup = document.getElementById('warningPopup');
            const cancelLeave = document.getElementById('cancelLeave');
            const confirmLeave = document.getElementById('confirmLeave');
            const fotoInput = document.getElementById('fotoInput');
            const profileImage = document.getElementById('profileImage');
            const pictureOverlay = document.getElementById('pictureOverlay');
            const fotoTemp = document.getElementById('fotoTemp');

            // Estado inicial del formulario
            const initialData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                password: '',
                password_confirm: '',
                foto: profileImage.src
            };

            let hasChanges = false;
            let tempImageFile = null;

            // Detectar cambios en los inputs
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', checkForChanges);
            });

            // Subida de imagen temporal
            pictureOverlay.addEventListener('click', function(e) {
                e.stopPropagation();
                fotoInput.click();
            });

            fotoInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const file = e.target.files[0];
                    
                    // Validaciones
                    if (file.size > 5 * 1024 * 1024) {
                        showPopup('Error', 'La imagen no debe superar los 5MB', 'error');
                        return;
                    }

                    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!validTypes.includes(file.type)) {
                        showPopup('Error', 'Solo se permiten imágenes JPEG, PNG, GIF o WebP', 'error');
                        return;
                    }

                    // Guardar archivo temporalmente
                    tempImageFile = file;

                    // Mostrar preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profileImage.src = e.target.result;
                        hasChanges = true;
                        updateSubmitButton();
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Verificar cambios
            function checkForChanges() {
                const currentData = {
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value,
                    password_confirm: document.getElementById('password_confirm').value,
                    foto: profileImage.src
                };

                hasChanges = 
                    currentData.name !== initialData.name ||
                    currentData.email !== initialData.email ||
                    currentData.password !== initialData.password ||
                    currentData.password_confirm !== initialData.password_confirm ||
                    currentData.foto !== initialData.foto;

                updateSubmitButton();
            }

            // Actualizar estado del botón Guardar
            function updateSubmitButton() {
                submitBtn.disabled = !hasChanges;
            }

            // Validación del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirm').value;

                // Validar contraseñas
                if (password && password !== passwordConfirm) {
                    showPopup('Error', 'Las contraseñas no coinciden', 'error');
                    return;
                }

                // Si hay imagen nueva, subirla primero
                if (tempImageFile) {
                    uploadTempImage().then(() => {
                        submitForm();
                    }).catch(error => {
                        showPopup('Error', 'Error al subir la imagen', 'error');
                    });
                } else {
                    submitForm();
                }
            });

            // Subir imagen temporal
            function uploadTempImage() {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('foto_perfil', tempImageFile);

                    fetch('<?= base_url('perfil/uploadTempImage') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fotoTemp.value = data.temp_filename;
                            resolve();
                        } else {
                            reject(data.message);
                        }
                    })
                    .catch(error => {
                        reject(error);
                    });
                });
            }

            // Enviar formulario
            function submitForm() {
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPopup('Éxito', data.message, 'success');
                        // Resetear estado
                        hasChanges = false;
                        tempImageFile = null;
                        initialData.name = document.getElementById('name').value;
                        initialData.email = document.getElementById('email').value;
                        updateSubmitButton();
                    } else {
                        if (data.errors) {
                            let errorMessage = '';
                            for (const error in data.errors) {
                                errorMessage += data.errors[error] + '\n';
                            }
                            showPopup('Error', errorMessage, 'error');
                        } else {
                            showPopup('Error', data.message, 'error');
                        }
                    }
                })
                .catch(error => {
                    showPopup('Error', 'Error al guardar los cambios', 'error');
                });
            }

            // Manejar cancelación
            cancelBtn.addEventListener('click', function() {
                if (hasChanges) {
                    warningPopup.style.display = 'flex';
                } else {
                    window.location.href = '<?= base_url('panel') ?>';
                }
            });

            cancelLeave.addEventListener('click', function() {
                warningPopup.style.display = 'none';
            });

            confirmLeave.addEventListener('click', function() {
                window.location.href = '<?= base_url('panel') ?>';
            });

            // Función para mostrar popup
            function showPopup(title, message, type) {
                const popup = document.createElement('div');
                popup.className = 'popup-overlay';
                popup.innerHTML = `
                    <div class="popup-container">
                        <div class="popup-icon ${type}">
                            <i class="ri-${type === 'success' ? 'check' : 'error-warning'}-line"></i>
                        </div>
                        <h3 class="popup-title">${title}</h3>
                        <p class="popup-message">${message}</p>
                        <button class="popup-button success" onclick="this.closest('.popup-overlay').remove()">
                            Aceptar
                        </button>
                    </div>
                `;
                document.body.appendChild(popup);
            }

            // Prevenir salida con cambios sin guardar
            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        });
    </script>
</body>
</html>