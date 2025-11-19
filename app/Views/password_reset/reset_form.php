<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nueva Contraseña' ?></title>
    <link rel="stylesheet" href="<?= base_url('styles/password_reset.css') ?>">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <section>
        <div class="wave">
            <span></span>
            <span></span>
            <span></span>
        </div>
        
        <div class="reset-container">
            <div class="reset-card">
                <!-- Icono -->
                <div class="reset-icon">
                    🔑
                </div>

                <!-- Título -->
                <h1 class="reset-title">Nueva Contraseña</h1>
                <p class="reset-subtitle">
                    Ingresa tu nueva contraseña para <strong><?= htmlspecialchars($email) ?></strong>
                </p>

                <!-- Formulario -->
                <form id="resetForm" class="reset-form">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="form-group">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input"
                               placeholder="Mínimo 6 caracteres"
                               required>
                        <div id="password-error" class="form-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm" class="form-label">Confirmar Contraseña</label>
                        <input type="password" 
                               id="password_confirm" 
                               name="password_confirm" 
                               class="form-input"
                               placeholder="Repite tu nueva contraseña"
                               required>
                        <div id="password_confirm-error" class="form-error"></div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-submit">
                        Cambiar contraseña
                    </button>
                </form>
            </div>
        </div>

        <!-- Popup de éxito -->
        <div id="successPopup" class="popup-overlay-reset">
            <div class="popup-reset success">
                <div class="popup-icon-reset">✓</div>
                <h3 class="popup-title-reset">¡Contraseña actualizada!</h3>
                <p class="popup-message-reset">Tu contraseña ha sido cambiada correctamente. Serás redirigido al login...</p>
            </div>
        </div>
    </section>

    <script>
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Limpiar errores previos
            clearAllErrors();
            
            const formData = new FormData(form);
            const password = formData.get('password');
            const passwordConfirm = formData.get('password_confirm');

            // Validaciones básicas
            if (!password || !passwordConfirm) {
                showFieldError('password', 'Debes completar ambos campos');
                return;
            }

            if (password.length < 6) {
                showFieldError('password', 'La contraseña debe tener al menos 6 caracteres');
                return;
            }

            if (password !== passwordConfirm) {
                showFieldError('password_confirm', 'Las contraseñas no coinciden');
                return;
            }

            // Deshabilitar botón
            submitBtn.disabled = true;
            submitBtn.textContent = 'Actualizando...';

            try {
                const response = await fetch('<?= base_url('password-reset/update') ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showSuccessPopup();
                    
                    // Redirigir al login después de 2 segundos
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                } else {
                    if (data.field) {
                        showFieldError(data.field, data.message);
                    } else {
                        alert(data.message);
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Cambiar contraseña';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud. Intenta nuevamente.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Cambiar contraseña';
            }
        });

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            
            if (field && errorDiv) {
                field.classList.add('error');
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function clearFieldError(fieldId) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            
            if (field && errorDiv) {
                field.classList.remove('error');
                errorDiv.classList.remove('show');
            }
        }

        function clearAllErrors() {
            ['password', 'password_confirm'].forEach(clearFieldError);
        }

        function showSuccessPopup() {
            document.getElementById('successPopup').classList.add('active');
        }

        // Limpiar error al escribir
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                clearFieldError(this.id);
            });
        });
    </script>
</body>
</html>