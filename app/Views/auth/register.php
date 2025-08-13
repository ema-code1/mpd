<div class="row justify-content-center">
  <div class="col-md-6">
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
      <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input class="form-control" type="text" name="name" value="<?= old('name') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="<?= old('email') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input class="form-control" type="password" name="password">
      </div>

      <div class="mb-3">
        <label class="form-label">Rol</label>
        <select id="roleSelect" name="role" class="form-select">
          <option value="comprador" <?= old('role')=='comprador' ? 'selected':'' ?>>Comprador</option>
          <option value="vendedor" <?= old('role')=='vendedor' ? 'selected':'' ?>>Vendedor</option>
          <option value="administrador" <?= old('role')=='administrador' ? 'selected':'' ?>>Administrador</option>
        </select>
      </div>

      <div id="vendorKeyDiv" class="mb-3" style="display: none;">
        <label class="form-label">Clave de vendedor (segunda contraseña)</label>
        <input class="form-control" type="password" name="vendor_key">
        <div class="form-text">Sólo si elegiste el rol vendedor.</div>
      </div>

      <button class="btn btn-primary" type="submit">Registrarse</button>
    </form>
  </div>
</div>

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
