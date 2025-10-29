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
    // Detectar cambios en inputs
    elements.form.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', handleFormChange);
    });

    // Subida de imagen
    elements.pictureOverlay.addEventListener('click', handleImageUploadClick);
    elements.fotoInput.addEventListener('change', handleImageSelect);

    // Botones
    elements.form.addEventListener('submit', handleFormSubmit);
    elements.cancelBtn.addEventListener('click', handleCancel);
    elements.cancelLeave.addEventListener('click', hideWarningPopup);
    elements.confirmLeave.addEventListener('click', confirmLeave);

    // Prevenir salida con cambios
    window.addEventListener('beforeunload', handleBeforeUnload);
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

    if (!validatePasswords()) return;

    try {
        await submitForm();
    } catch (error) {
        showPopup('Error', 'Error al procesar la solicitud', 'error');
    }
}

function validatePasswords() {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;

    if (password && password !== passwordConfirm) {
        showPopup('Error', 'Las contraseñas no coinciden', 'error');
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
    showPopup('Éxito', data.message, 'success');
    resetFormState();
}

function handleError(data) {
    let errorMessage = data.message;
    
    if (data.errors) {
        errorMessage = Object.values(data.errors).join('\n');
    }
    
    showPopup('Error', errorMessage, 'error');
}

function resetFormState() {
    profileState.hasChanges = false;
    profileState.tempImageFile = null;
    profileState.initialData = getFormData();
    updateSubmitButton();
}

// Manejo de navegación
function handleCancel() {
    if (profileState.hasChanges) {
        showWarningPopup();
    } else {
        redirectToPanel();
    }
}

function showWarningPopup() {
    document.getElementById('warningPopup').style.display = 'flex';
}

function hideWarningPopup() {
    document.getElementById('warningPopup').style.display = 'none';
}

function confirmLeave() {
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

// Utilidades
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
    </script>
</body>
</html>