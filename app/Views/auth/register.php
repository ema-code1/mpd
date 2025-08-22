<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrarse</title>
  <link rel="stylesheet" href="<?= base_url('styles/register.css') ?>">
</head>
<body>
  <div class="container-container">
    <div class="register-container">
    <h2>Registro de usuario</h2> // CHAU

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
      <input class="form-control" type="text" name="name" value="<?= old('name') ?>">

<<<<<<< HEAD
      <div class="mb-3">
    <label class="form-label">Rol</label>
    <select id="roleSelect" name="role" class="form-select">
        <option value="comprador">Comprador</option>
        <option value="administrador">Administrador</option>
    </select>
</div>

<div id="adminKeyDiv" class="mb-3" style="display: none;">
    <label class="form-label">Clave de administrador</label>
    <input class="form-control" type="password" name="admin_key">
    <div class="form-text">Solo si eliges administrador.</div>
</div>
=======
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" value="<?= old('email') ?>">

      <label class="form-label">Contraseña</label>
      <input class="form-control" type="password" name="password">

      <label class="form-label">Rol</label>
      <select id="roleSelect" name="role" class="form-select">
        <option value="comprador" <?= old('role')=='comprador' ? 'selected':'' ?>>Comprador</option>
        <option value="vendedor" <?= old('role')=='vendedor' ? 'selected':'' ?>>Vendedor</option>
        <option value="administrador" <?= old('role')=='administrador' ? 'selected':'' ?>>Administrador</option>
      </select>

      <div id="vendorKeyDiv" style="display: none;">
        <label class="form-label">Clave de vendedor (segunda contraseña)</label>
        <input class="form-control" type="password" name="vendor_key">
        <div class="form-text">Sólo si elegiste el rol vendedor.</div>
      </div>
>>>>>>> 463cda1a8b0e6b06fb4dd406dfd2326f85167b5e

      <button class="btn-submit" type="submit">Registrarse</button>
    </form>
  </div>
  </div>

<<<<<<< HEAD
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
=======
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const roleSelect = document.getElementById('roleSelect');
      const vendorKeyDiv = document.getElementById('vendorKeyDiv');

      function toggleVendor() {
        if (roleSelect.value === 'vendedor') vendorKeyDiv.style.display = 'block';
        else vendorKeyDiv.style.display = 'none';
      }
      roleSelect.addEventListener('change', toggleVendor);
      toggleVendor();
    });
  </script>
</body>
</html>
>>>>>>> 463cda1a8b0e6b06fb4dd406dfd2326f85167b5e
