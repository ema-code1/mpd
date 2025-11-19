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
                </div>
            </div>

            <!-- Formulario -->
            <form id="profileForm" action="<?= base_url('perfil/actualizar') ?>" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" id="name" name="name" class="form-input"
                           value="<?= old('name', $user['name'] ?? '') ?>" 
                           placeholder="Tu nombre completo">
                    <div id="name-error" class="form-error"></div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" id="email" name="email" class="form-input"
                           value="<?= old('email', $user['email'] ?? '') ?>" 
                           placeholder="tu@email.com">
                    <div id="email-error" class="form-error"></div>
                </div>

                <!-- 🔐 SECCIÓN DE CONTRASEÑAS -->
                <div class="password-section">
                    <div class="password-section-title">Cambiar Contraseña (Opcional)</div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Mínimo 6 caracteres">
                        <div id="password-error" class="form-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" id="password_confirm" name="password_confirm" class="form-input"
                               placeholder="Repite la nueva contraseña">
                        <div id="password_confirm-error" class="form-error"></div>
                    </div>

                    <!-- 🔑 BOTÓN OLVIDÉ MI CONTRASEÑA (IGUAL AL LOGIN) -->
                    <div class="forgot-password-link">
                        <a href="<?= base_url('password-reset') ?>" class="forgot-link">
                            <i class="ri-lock-unlock-line"></i>
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <!-- 🔑 CONTRASEÑA ACTUAL (OBLIGATORIO PARA GUARDAR CAMBIOS) -->
                <div class="form-group" style="margin-top: 1.5rem; border-top: 2px solid #e0e0e0; padding-top: 1.5rem;">
                    <label for="current_password" class="form-label">
                        Contraseña Actual
                        <span class="required-indicator">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" class="form-input"
                           placeholder="Ingresa tu contraseña actual para confirmar los cambios">
                    <div id="current_password-error" class="form-error"></div>
                </div>

                <div class="form-buttons">
                    <button type="button" id="cancelBtn" class="btn btn-cancel">
                        Cancelar
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-submit" disabled>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup de advertencia -->
    <div id="warningPopup" class="popup-overlay-perfil">
        <div class="popup-perfil warning">
            <div class="popup-icon-perfil">⚠️</div>
            <h3 class="popup-title-perfil">Cambios sin guardar</h3>
            <p class="popup-message-perfil">Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?</p>
            <div class="popup-buttons-perfil">
                <button type="button" class="popup-btn-perfil confirm" id="confirmLeave">
                    Sí, salir
                </button>
                <button type="button" class="popup-btn-perfil cancel" id="cancelLeave">
                    Seguir editando
                </button>
            </div>
        </div>
    </div>
</section>

    <script>
// Estado global
const profileState = {
    initialData: {},
    hasChanges: false,
    tempImageFile: null
};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeProfileForm();
});

function initializeProfileForm() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // Elementos del DOM
    const elements = {
        form: form,
        cancelBtn: document.getElementById('cancelBtn'),
        submitBtn: document.getElementById('submitBtn'),
        warningPopup: document.getElementById('warningPopup'),
        cancelLeave: document.getElementById('cancelLeave'),
        confirmLeave: document.getElementById('confirmLeave'),
        fotoInput: document.getElementById('fotoInput'),
        profileImage: document.getElementById('profileImage'),
        pictureOverlay: document.getElementById('pictureOverlay')
    };

    // Estado inicial
    profileState.initialData = getFormData();
    
    // Event listeners
    setupEventListeners(elements);
}

function getFormData() {
    return {
        name: document.getElementById('name').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value,
        password_confirm: document.getElementById('password_confirm').value,
        foto: document.getElementById('profileImage').src
    };
}

function setupEventListeners(elements) {
    console.log('🎯 Configurando event listeners...');
    
    // Detectar cambios en inputs
    elements.form.querySelectorAll('input:not(#current_password)').forEach(input => {
        input.addEventListener('input', handleFormChange);
    });

    // Limpiar error al escribir
    elements.form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function() {
            clearFieldError(this.id);
        });
    });

    // Subida de imagen
    elements.pictureOverlay.addEventListener('click', handleImageUploadClick);
    elements.fotoInput.addEventListener('change', handleImageSelect);

    // Botones
    elements.form.addEventListener('submit', handleFormSubmit);
    
    // 🔴 BOTÓN CANCELAR - Event listener mejorado
    if (elements.cancelBtn) {
        console.log('✅ Botón Cancelar encontrado, agregando listener');
        elements.cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🔴 Click en botón Cancelar detectado');
            handleCancel();
        });
    } else {
        console.error('❌ Botón Cancelar NO encontrado');
    }
    
    // Botones del popup de advertencia
    if (elements.cancelLeave) {
        console.log('✅ Botón "Seguir editando" encontrado');
        elements.cancelLeave.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('🔙 Click en Seguir editando');
            hideWarningPopup();
        });
    }
    
    if (elements.confirmLeave) {
        console.log('✅ Botón "Sí, salir" encontrado');
        elements.confirmLeave.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('👋 Click en Sí, salir');
            confirmLeave();
        });
    }

    // Prevenir salida con cambios
    window.addEventListener('beforeunload', handleBeforeUnload);
}

// 🔴 Funciones para manejo de errores
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    if (field && errorDiv) {
        field.classList.add('error');
        errorDiv.textContent = message;
        errorDiv.classList.add('show');
        
        // Hacer scroll al campo con error
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById(`${fieldId}-error`);
    
    if (field && errorDiv) {
        field.classList.remove('error');
        errorDiv.classList.remove('show');
        errorDiv.textContent = '';
    }
}

function clearAllErrors() {
    const errorDivs = document.querySelectorAll('.form-error');
    const errorFields = document.querySelectorAll('.form-input.error');
    
    errorDivs.forEach(div => {
        div.classList.remove('show');
        div.textContent = '';
    });
    
    errorFields.forEach(field => {
        field.classList.remove('error');
    });
}

// Manejo de cambios en el formulario
function handleFormChange() {
    const currentData = getFormData();
    profileState.hasChanges = hasFormChanged(currentData);
    updateSubmitButton();
}

function hasFormChanged(currentData) {
    return Object.keys(profileState.initialData).some(key => 
        currentData[key] !== profileState.initialData[key]
    );
}

function updateSubmitButton() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = !profileState.hasChanges;
    }
}

// Manejo de imágenes
function handleImageUploadClick(e) {
    e.stopPropagation();
    document.getElementById('fotoInput').click();
}

function handleImageSelect(e) {
    const file = e.target.files[0];
    if (!file) return;

    if (!validateImage(file)) return;

    profileState.tempImageFile = file;
    displayImagePreview(file);
    profileState.hasChanges = true;
    updateSubmitButton();
}

function validateImage(file) {
    if (file.size > 5 * 1024 * 1024) {
        showPopup('Error', 'La imagen no debe superar los 5MB', 'error');
        return false;
    }

    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
        showPopup('Error', 'Solo se permiten imágenes JPEG, PNG, GIF o WebP', 'error');
        return false;
    }

    return true;
}

function displayImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('profileImage').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Envío del formulario
async function handleFormSubmit(e) {
    e.preventDefault();
    
    // Limpiar errores previos
    clearAllErrors();

    // Validar contraseña actual
    const currentPassword = document.getElementById('current_password').value;
    if (!currentPassword || currentPassword.trim() === '') {
        showFieldError('current_password', 'Debes ingresar tu contraseña actual para confirmar los cambios');
        return;
    }

    if (!validatePasswords()) return;

    try {
        // Mostrar indicador de carga
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Guardando...';

        await submitForm();

        // Restaurar botón
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    } catch (error) {
        console.error('Error:', error);
        showPopup('Error', 'Error al procesar la solicitud', 'error');
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Guardar Cambios';
    }
}

function validatePasswords() {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;

    if (password && password !== passwordConfirm) {
        showFieldError('password_confirm', 'Las contraseñas no coinciden');
        return false;
    }
    
    if (password && password.length < 6) {
        showFieldError('password', 'La contraseña debe tener al menos 6 caracteres');
        return false;
    }
    
    return true;
}

// Operaciones con API
function submitForm() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);
    
    // Agregar la imagen al FormData si existe
    if (profileState.tempImageFile) {
        formData.append('foto_perfil', profileState.tempImageFile);
    }

    return fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            handleSuccess(data);
        } else {
            handleError(data);
        }
    });
}

function handleSuccess(data) {
    // Actualizar imagen de perfil si cambió
    if (data.foto_perfil) {
        const newImageUrl = '<?= base_url('ppimages/') ?>' + data.foto_perfil + '?' + new Date().getTime();
        document.getElementById('profileImage').src = newImageUrl;
    }
    
    showPopup('¡Perfil Actualizado!', data.message, 'success');
    resetFormState();
    
    // Limpiar contraseña actual
    document.getElementById('current_password').value = '';
}

function handleError(data) {
    // Mostrar error en el campo específico si se especifica
    if (data.field) {
        showFieldError(data.field, data.message);
    } else {
        showPopup('Error', data.message, 'error');
    }
}

function resetFormState() {
    profileState.hasChanges = false;
    profileState.tempImageFile = null;
    
    // No incluir current_password en el estado inicial
    profileState.initialData = getFormData();
    
    // Limpiar campos de nueva contraseña
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
    
    updateSubmitButton();
}

// Manejo de navegación
function handleCancel() {
    console.log('🔴 handleCancel ejecutado');
    console.log('Tiene cambios:', profileState.hasChanges);
    
    if (profileState.hasChanges) {
        showWarningPopup();
    } else {
        redirectToPanel();
    }
}

function showWarningPopup() {
    console.log('⚠️ Mostrando popup de advertencia');
    const popup = document.getElementById('warningPopup');
    if (popup) {
        popup.classList.add('active');
    }
}

function hideWarningPopup() {
    console.log('✅ Ocultando popup de advertencia');
    const popup = document.getElementById('warningPopup');
    if (popup) {
        popup.classList.remove('active');
    }
}

function confirmLeave() {
    console.log('👋 Confirmado salir - Redirigiendo...');
    redirectToPanel();
}

function redirectToPanel() {
    window.location.href = '<?= base_url('panel') ?>';
}

function handleBeforeUnload(e) {
    if (profileState.hasChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
}

// Cerrar popup al hacer clic en el overlay
document.addEventListener('DOMContentLoaded', function() {
    const warningPopup = document.getElementById('warningPopup');
    if (warningPopup) {
        warningPopup.addEventListener('click', function(e) {
            if (e.target === warningPopup) {
                hideWarningPopup();
            }
        });
    }
});

// Utilidades
function showPopup(title, message, type) {
    // Definir el emoji/ícono según el tipo
    let iconEmoji = '✓';
    if (type === 'success') {
        iconEmoji = '✓';
    } else if (type === 'error') {
        iconEmoji = '✕';
    } else if (type === 'warning') {
        iconEmoji = '⚠️';
    }
    
    const popup = document.createElement('div');
    popup.className = 'popup-overlay-perfil';
    popup.innerHTML = `
        <div class="popup-perfil ${type}">
            <div class="popup-icon-perfil">${iconEmoji}</div>
            <h3 class="popup-title-perfil">${title}</h3>
            <p class="popup-message-perfil">${message}</p>
            <div class="popup-buttons-perfil">
                <button class="popup-btn-perfil confirm" onclick="this.closest('.popup-overlay-perfil').remove()">
                    Aceptar
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(popup);
    
    // Activar el popup con animación
    setTimeout(() => {
        popup.classList.add('active');
    }, 10);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        if (popup && popup.parentNode) {
            popup.classList.remove('active');
            setTimeout(() => {
                popup.remove();
            }, 300);
        }
    }, 5000);
    
    // Cerrar al hacer clic en el overlay
    popup.addEventListener('click', function(e) {
        if (e.target === popup) {
            popup.classList.remove('active');
            setTimeout(() => {
                popup.remove();
            }, 300);
        }
    });
}
    </script>
</body>
</html>