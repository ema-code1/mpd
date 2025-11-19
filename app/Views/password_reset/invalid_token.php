<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Enlace Inválido' ?></title>
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
                <div class="reset-icon error">
                    ⏱️
                </div>

                <!-- Título -->
                <h1 class="reset-title">Enlace Inválido o Expirado</h1>
                <p class="reset-subtitle">
                    <?= $message ?? 'El enlace de recuperación que usaste es inválido o ya expiró.' ?>
                </p>

                <div class="info-box">
                    <p><strong>¿Qué puedes hacer?</strong></p>
                    <ul style="text-align: left; margin: 10px 0;">
                        <li>Los enlaces expiran después de 12 horas</li>
                        <li>Solo puedes usar cada enlace una vez</li>
                        <li>Solicita un nuevo enlace de recuperación</li>
                    </ul>
                </div>

                <a href="<?= base_url('password-reset') ?>" class="btn btn-submit">
                    Solicitar nuevo enlace
                </a>

                <a href="<?= base_url('login') ?>" class="back-link">
                    ← Volver al login
                </a>
            </div>
        </div>
    </section>
</body>
</html>