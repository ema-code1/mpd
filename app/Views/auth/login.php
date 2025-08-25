    <!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="<?= base_url('styles/login.css') ?>">
</head>
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
    <div id="loginAdminDiv" class="mb-3" style="display:none;">
     <label class="form-label">Clave de administrador</label>
    <input class="form-control" type="password" name="admin_key">
    </div>
        <form action="<?= site_url('login-post') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="styled-input" type="email" name="email" value="<?= old('email') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input class="styled-input" type="password" name="password">
            </div>

            <div class="form-group">
                <label class="form-label">Selecciona rol</label>
                <select id="loginRole" name="role" class="styled-input">
                    <option value="comprador">Comprador</option>
                    <option value="vendedor">Vendedor</option>
                    <option value="administrador">Administrador</option>
                </select>
            </div>

            <div id="loginVendorDiv" class="form-group" style="display:none;">
                <label class="form-label">Clave de vendedor (si eliges vendedor)</label>
                <input class="styled-input" type="password" name="vendor_key">
            </div>

            <button class="login-btn" type="submit">Ingresar</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const loginRole = document.getElementById('loginRole');
  const loginAdminDiv = document.getElementById('loginAdminDiv');

  function toggleAdminLogin() {
    loginAdminDiv.style.display = (loginRole.value === 'administrador') ? 'block' : 'none';
  }
  loginRole.addEventListener('change', toggleAdminLogin);
  toggleAdminLogin();
    const loginRole = document.getElementById('loginRole');
    const loginVendorDiv = document.getElementById('loginVendorDiv');

    function toggleVendorLogin() {
        loginVendorDiv.style.display = (loginRole.value === 'vendedor') ? 'block' : 'none';
    }
    loginRole.addEventListener('change', toggleVendorLogin);
    toggleVendorLogin();
});
</script>

</body>
</html>
