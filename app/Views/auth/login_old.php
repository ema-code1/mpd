<div class="row justify-content-center">
  <div class="col-md-6">
    <h2>Iniciar sesión</h2>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('login-post') ?>" method="post" novalidate>
      <?= csrf_field() ?>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" value="<?= old('email') ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input class="form-control" type="password" name="password">
      </div>

      <div class="mb-3">
        <label class="form-label">Selecciona rol</label>
        <select id="loginRole" name="role" class="form-select">
          <option value="comprador">Comprador</option>
          <option value="vendedor">Vendedor</option>
          <option value="administrador">Administrador</option>
        </select>
      </div>

      <div id="loginVendorDiv" class="mb-3" style="display:none;">
        <label class="form-label">Clave de vendedor (si eliges vendedor)</label>
        <input class="form-control" type="password" name="vendor_key">
      </div>

      <button class="btn btn-primary" type="submit">Ingresar</button>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const loginRole = document.getElementById('loginRole');
  const loginVendorDiv = document.getElementById('loginVendorDiv');

  function toggleVendorLogin() {
    loginVendorDiv.style.display = (loginRole.value === 'vendedor') ? 'block' : 'none';
  }
  loginRole.addEventListener('change', toggleVendorLogin);
  toggleVendorLogin();
});
</script>