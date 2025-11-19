<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Recuperar Contraseña' ?></title>
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
                    🔐
                </div>

                <!-- Título -->
                <h1 class="reset-title">¿Olvidaste tu contraseña?</h1>
                <p class="reset-subtitle">
                    Ingresa tu email y te enviaremos un enlace para recuperar tu cuenta
                </p>

                <!-- Formulario -->
                <form id="resetForm" class="reset-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input"
                               placeholder="tu@email.com"
                               required>
                        <div id="email-error" class="form-error"></div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-submit">
                        Enviar enlace de recuperación
                    </button>

                    <a href="<?= base_url('login') ?>" class="back-link">
                        ← Volver al login
                    </a>
                </form>
            </div>
        </div>

        <!-- Popup de éxito/error -->
        <div id="messagePopup" class="popup-overlay-reset">
            <div class="popup-reset success">
                <div class="popup-icon-reset">✓</div>
                <h3 class="popup-title-reset">¡Listo!</h3>
                <p class="popup-message-reset"></p>
                <button class="popup-btn-reset confirm" onclick="window.location.href='<?= base_url('login') ?>'">
                    Ir al login
                </button>
            </div>
        </div>
    </section>

    <script>
        const form = document.getElementById('resetForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Limpiar errores previos
            clearFieldError('email');
            
            const email = document.getElementById('email').value;
            
            if (!email) {
                showFieldError('email', 'Por favor ingresa tu email');
                return;
            }

            // Deshabilitar botón y mostrar loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Enviando...';

            try {
                const response = await fetch('<?= base_url('password-reset/send') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `email=${encodeURIComponent(email)}`
                });

                const data = await response.json();

                if (data.success) {
                    showSuccessPopup(data.message);
                } else {
                    if (data.field) {
                        showFieldError(data.field, data.message);
                    } else {
                        alert(data.message);
                    }
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Enviar enlace de recuperación';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud. Intenta nuevamente.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Enviar enlace de recuperación';
            }
        });

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(`${fieldId}-error`);
            
            if (field && errorDiv) {
                field.classList.add('error');
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
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

        function showSuccessPopup(message) {
            const popup = document.getElementById('messagePopup');
            const messageElement = popup.querySelector('.popup-message-reset');
            
            messageElement.textContent = message;
            popup.classList.add('active');
        }

        // Limpiar error al escribir
        document.getElementById('email').addEventListener('input', function() {
            clearFieldError('email');
        });
    </script>
</body>
</html>