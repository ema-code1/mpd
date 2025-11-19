<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url('styles/login.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
</head>
<section>
  <div class="wave">
    <span></span>
    <span></span>
    <span></span>
  </div>
<body class="login-body">
<div class="login-container">
    <div class="login-box">
        <h2 class="login-title">Iniciar sesión</h2>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <form action="<?= site_url('login-post') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="styled-input" type="email" name="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input class="styled-input" type="password" name="password" required>
            </div>

            <div class="form-group">
                <label class="form-label">Selecciona rol</label>
                <select id="loginRole" name="role" class="form-select" required>
                    <option value="comprador">Comprador</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>

            <div id="loginAdminDiv" class="form-group" style="display:none;">
                <label class="form-label">Clave de administrador</label>
                    <input class="styled-input" type="password" name="admin_key">
            </div>

            <button class="login-btn" type="submit">Ingresar</button>
            
            <!-- 🔑 BOTÓN OLVIDÉ MI CONTRASEÑA -->
            <div class="forgot-password-link">
                <a href="<?= base_url('password-reset') ?>" class="forgot-link">
                    <i class="ri-lock-unlock-line"></i>
                    ¿Olvidaste tu contraseña?
                </a>
            </div>
        </form>
    </div>
</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const loginRole = document.getElementById('loginRole');
  const loginAdminDiv = document.getElementById('loginAdminDiv');

  function toggleAdminLogin() {
    loginAdminDiv.style.display = (loginRole.value === 'administrador') ? 'block' : 'none';
  }
  loginRole.addEventListener('change', toggleAdminLogin);
  toggleAdminLogin();
});
</script>
</body>
</html>