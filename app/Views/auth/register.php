<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse</title>
  <link rel="stylesheet" href="<?= base_url('styles/register.css') ?>">
</head>
<body>
  <div class="container-container">
    <div class="register-container">
      <h2>Registro de usuario</h2>

      <?php if(session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
          <?php foreach(session()->getFlashdata('errors') as $err): ?>
            <div><?= esc($err) ?></div>
          <?php endforeach ?>
        </div>
      <?php endif; ?>

      <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form action="<?= site_url('register-post') ?>" method="post" novalidate>
        <?= csrf_field() ?>

        <label class="form-label">Nombre</label>
        <input class="form-control" type="text" name="name" value="<?= old('name') ?>" required>

        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="<?= old('email') ?>" required>

        <label class="form-label">Contraseña</label>
        <input class="form-control" type="password" name="password" required>

        <div class="mb-3">
          <label class="form-label">Rol</label>
          <select id="roleSelect" name="role" class="form-select" required>
            <option value="comprador">Comprador</option>
            <option value="administrador">Administrador</option>
          </select>
        </div>

        <div id="adminKeyDiv" class="mb-3" style="display: none;">
          <label class="form-label">Clave de administrador</label>
          <input class="form-control" type="password" name="admin_key">
          <div class="form-text">Solo si eliges administrador.</div>
        </div>

        <button class="btn-submit" type="submit">Registrarse</button>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const roleSelect = document.getElementById('roleSelect');
      const adminKeyDiv = document.getElementById('adminKeyDiv');

      function toggleAdmin() {
        adminKeyDiv.style.display = (roleSelect.value === 'administrador') ? 'block' : 'none';
      }
      roleSelect.addEventListener('change', toggleAdmin);
      toggleAdmin();
    });
  </script>
</body>
</html>
